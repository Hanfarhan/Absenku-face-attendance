<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\FaceEmbedding;
use App\Models\FaceSample;

class FaceRegisterController extends Controller
{
    // URL Python API
    private string $apiUrl = 'http://127.0.0.1:8001';

    // ── Simpan foto & embedding ───────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'captures'   => 'required|array|min:10|max:10',
            'captures.*' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            // ── 1. Kirim foto ke Python API ───────────────────
            $response = Http::timeout(60)->post("{$this->apiUrl}/register", [
                'captures' => $request->captures,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Python API error: ' . $response->json('detail', 'Unknown error'),
                ], 500);
            }

            $result = $response->json();

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak berhasil diproses — pastikan pencahayaan cukup',
                ], 422);
            }

            // ── 2. Simpan foto ke storage ─────────────────────
            $folder = "face_samples/{$user->id}";
            Storage::disk('local')->deleteDirectory($folder); // hapus foto lama

            foreach ($request->captures as $index => $base64) {
                // Hapus prefix base64
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                $imageData = base64_decode($imageData);

                $filename  = "sample_" . ($index + 1) . ".jpg";
                Storage::disk('local')->put("{$folder}/{$filename}", $imageData);

                // Simpan path ke database
                FaceSample::create([
                    'user_id'      => $user->id,
                    'file_path'    => "{$folder}/{$filename}",
                    'sample_index' => $index + 1,
                ]);
            }

            // ── 3. Simpan embedding ke database ──────────────
            // Nonaktifkan embedding lama
            FaceEmbedding::deactivateAllForUser($user->id);

            // Simpan embedding baru
            FaceEmbedding::create([
                'user_id'   => $user->id,
                'embedding' => $result['embedding'],
                'is_active' => true,
            ]);

            // ── 4. Update face_registered ─────────────────────
            $user->update(['face_registered' => true]);

            return response()->json([
                'success'        => true,
                'message'        => 'Wajah berhasil didaftarkan',
                'success_photos' => $result['success_photos'],
                'failed_photos'  => $result['failed_photos'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}