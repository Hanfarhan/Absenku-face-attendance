<?php

namespace App\Filament\Pages;

use App\Models\Threshold;
use App\Models\LivenessThreshold;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThresholdSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.threshold-settings';

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-adjustments-horizontal';
    }

    public static function getNavigationLabel(): string
    {
        return 'Threshold Settings';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penelitian';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public function getTitle(): string
    {
        return 'Pengaturan Threshold';
    }

    // ── State form FaceNet ────────────────────────────────────
    public float  $facenet_value     = 0.75;
    public string $facenet_note      = '';

    // ── State form Liveness ───────────────────────────────────
    public float  $liveness_value    = 0.50;
    public string $liveness_note     = '';

    // ── Riwayat ───────────────────────────────────────────────
    public array  $facenetHistory    = [];
    public array  $livenessHistory   = [];

    public function mount(): void
    {
        // Load nilai threshold aktif saat ini
        $facenet  = Threshold::getActive();
        $liveness = LivenessThreshold::getActive();

        $this->facenet_value  = $facenet?->value  ?? 0.75;
        $this->liveness_value = $liveness?->value ?? 0.50;

        // Load riwayat 10 terakhir
        $this->facenetHistory  = Threshold::latest()->take(10)->get()->toArray();
        $this->livenessHistory = LivenessThreshold::latest()->take(10)->get()->toArray();
    }

    // ── Simpan FaceNet Threshold ──────────────────────────────

    public function saveFacenet(): void
    {
        $value = $this->facenet_value;

        if ($value < 0.0 || $value > 1.0) {
            Notification::make()
                ->title('Nilai tidak valid')
                ->body('Threshold harus antara 0.00 dan 1.00')
                ->danger()
                ->send();
            return;
        }

        // Simpan ke DB Laravel
        $record = Threshold::create([
            'value'   => round($value, 4),
            'set_by'  => Auth::id(),
            'note'    => $this->facenet_note ?: 'Diubah via dashboard',
            'is_active' => false,
        ]);
        $record->setAsActive();

        // Sync ke FastAPI
        $this->syncToFastApi(facenet: $value);

        // Refresh riwayat
        $this->facenetHistory = Threshold::latest()->take(10)->get()->toArray();
        $this->facenet_note   = '';

        Notification::make()
            ->title('FaceNet threshold diperbarui')
            ->body("Threshold baru: {$value} — berlaku sekarang")
            ->success()
            ->send();
    }

    // ── Simpan Liveness Threshold ─────────────────────────────

    public function saveLiveness(): void
    {
        $value = $this->liveness_value;

        if ($value < 0.0 || $value > 1.0) {
            Notification::make()
                ->title('Nilai tidak valid')
                ->body('Threshold harus antara 0.00 dan 1.00')
                ->danger()
                ->send();
            return;
        }

        // Simpan ke DB Laravel
        $record = LivenessThreshold::create([
            'value'     => round($value, 4),
            'set_by'    => Auth::id(),
            'note'      => $this->liveness_note ?: 'Diubah via dashboard',
            'is_active' => false,
        ]);
        $record->setAsActive();

        // Sync ke FastAPI
        $this->syncToFastApi(liveness: $value);

        // Refresh riwayat
        $this->livenessHistory = LivenessThreshold::latest()->take(10)->get()->toArray();
        $this->liveness_note   = '';

        Notification::make()
            ->title('Liveness threshold diperbarui')
            ->body("Threshold baru: {$value} — berlaku sekarang")
            ->success()
            ->send();
    }

    // ── Sync ke FastAPI ───────────────────────────────────────

    private function syncToFastApi(?float $facenet = null, ?float $liveness = null): void
    {
        $apiUrl = config('attendance.face_api_url', 'http://127.0.0.1:8001');

        $payload = array_filter([
            'facenet_threshold'  => $facenet,
            'liveness_threshold' => $liveness,
        ], fn($v) => $v !== null);

        try {
            Http::timeout(5)->put("{$apiUrl}/config/thresholds", $payload);
        } catch (\Exception $e) {
            Log::warning('Gagal sync threshold ke FastAPI: ' . $e->getMessage());
            // Tidak throw — DB sudah tersimpan, FastAPI akan baca saat restart
        }
    }
}
