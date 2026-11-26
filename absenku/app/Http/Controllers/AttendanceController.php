<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\AttendanceLog;
use App\Models\Threshold;
use App\Models\LivenessThreshold;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('attendance.face_api_url') ?? 'http://127.0.0.1:8001';
    }

    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'image'            => 'required|string',
            'attack_type'      => 'nullable|in:genuine,print_photo,digital_screen',
            'evaluation_mode'  => 'nullable|in:full_pipeline,facenet_only',
        ]);

        $user = Auth::user();

        if (!$user->face_registered) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum mendaftarkan wajah. Silakan daftarkan wajah terlebih dahulu.',
            ], 422);
        }

        $embeddings = $user->activeFaceEmbeddings()->pluck('embedding')->toArray();

        if (empty($embeddings)) {
            return response()->json([
                'success' => false,
                'message' => 'Data wajah tidak ditemukan. Silakan daftarkan wajah ulang.',
            ], 422);
        }

        // FaceNet threshold (dari tabel thresholds)
        $activeThreshold = Threshold::getActive();
        $thresholdValue  = $activeThreshold
            ? $activeThreshold->value
            : config('attendance.default_threshold');

        // Mode evaluasi: dikirim dari frontend saat toggle Sistem A/B aktif
        // Default: 'full_pipeline' (mode produksi normal)
        $evaluationMode = $request->input('evaluation_mode', 'full_pipeline');

        try {
            $response = Http::timeout(30)->post("{$this->apiUrl}/recognize", [
                'image'           => $request->image,
                'embeddings'      => $embeddings,
                'threshold'       => $thresholdValue,
                'evaluation_mode' => $evaluationMode,  // Sistem A atau B
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server pengenalan wajah.',
                ], 500);
            }

            $result = $response->json();

            $capturePath = $this->saveCapture($request->image, $user->id);

            $log = AttendanceLog::create([
                'user_id'          => $user->id,
                'confidence_score' => $result['confidence_score'],
                'liveness_score'   => $result['liveness_score'],
                'spoof_type'       => $result['spoof_type'] ?? null,
                'threshold_used'   => $thresholdValue,
                'result'           => $result['result'],
                'rejection_reason' => $result['rejection_reason'] ?? null,
                'capture_path'     => $capturePath,
                'attack_type'      => $request->attack_type ?? 'genuine',
                'evaluation_mode'  => $evaluationMode,  // catat Sistem A atau B
                'attempted_at'     => Carbon::now(),
            ]);

            if ($result['result'] === 'accepted') {
                $isLate = AttendanceHelper::isLate($log->attempted_at);

                return response()->json([
                    'success'          => true,
                    'result'           => 'accepted',
                    'message'          => 'Absensi berhasil! Identitas terverifikasi.',
                    'status'           => $isLate ? 'Terlambat' : 'Tepat Waktu',
                    'confidence_score' => round($result['confidence_score'] * 100, 1),
                    'liveness_score'   => round($result['liveness_score'] * 100, 1),
                    'time'             => $log->attempted_at->format('H:i'),
                ]);
            }

            return response()->json([
                'success'          => true,
                'result'           => 'rejected',
                'message'          => $this->getRejectionMessage($result['rejection_reason']),
                'rejection_reason' => $result['rejection_reason'] ?? null,   // ← field ini wajib ada agar frontend bisa bedakan spoof vs masalah lain
                'confidence_score' => round($result['confidence_score'] * 100, 1),
                'liveness_score'   => round($result['liveness_score'] * 100, 1),
                'spoof_type'       => $result['spoof_type'] ?? null,
            ]);

        } catch (\Exception $e) {
            \Log::error('AttendanceController error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' | Line: ' . $e->getLine(),
            ], 500);
        }
    }

    private function saveCapture(string $base64Image, int $userId): string
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $imageData = base64_decode($imageData);

        $folder   = "attendance_captures/{$userId}";
        $filename = Carbon::now()->format('Y_m_d_His') . '.jpg';

        Storage::disk('local')->put("{$folder}/{$filename}", $imageData);

        return "{$folder}/{$filename}";
    }

    private function getRejectionMessage(?string $reason): string
    {
        return match($reason) {
            'no_face_detected' => 'Wajah tidak terdeteksi. Pastikan wajah Anda terlihat jelas di kamera.',
            'multiple_faces'   => 'Terdeteksi lebih dari satu wajah. Pastikan hanya satu orang di depan kamera.',
            'spoof_detected'   => 'Absensi ditolak — terdeteksi percobaan spoofing. Gunakan wajah asli Anda.',
            'low_confidence'   => 'Absensi ditolak — wajah tidak cocok dengan data yang terdaftar.',
            default            => 'Absensi ditolak — silakan coba lagi.',
        };
    }
}