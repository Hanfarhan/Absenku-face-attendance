<?php

namespace App\Filament\Pages;

use App\Models\AttendanceLog;
use App\Models\Threshold;
use App\Models\LivenessThreshold;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ResearchDashboard extends Page
{
    protected string $view = 'filament.pages.research-dashboard';

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Research Dashboard';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penelitian';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string
    {
        return 'Research Dashboard';
    }

    // ── Filter state ─────────────────────────────────────────────
    public string  $filterMode      = 'all';   // all | full_pipeline | facenet_only
    public string  $filterAttack    = 'all';   // all | genuine | attack
    public ?string $filterThreshold = null;    // float as string | null

    // ── Computed metrics ─────────────────────────────────────────

    /**
     * Hitung semua metrik untuk KEDUA sistem sekaligus.
     * Dipakai oleh Blade view via $this->getMetrics().
     */
    public function getMetrics(): array
    {
        $th = $this->filterThreshold ? (float) $this->filterThreshold : null;

        return [
            'system_a' => [
                'label' => 'Sistem A — FaceNet Only',
                'far'   => AttendanceLog::calculateFAR($th, 'facenet_only'),
                'frr'   => AttendanceLog::calculateFRR($th, 'facenet_only'),
                'apcer' => null,
                'bpcer' => null,
                'total' => AttendanceLog::query()->byMode('facenet_only')->count(),
            ],
            'system_b' => [
                'label' => 'Sistem B — FaceNet + Liveness',
                'far'   => AttendanceLog::calculateFAR($th, 'full_pipeline'),
                'frr'   => AttendanceLog::calculateFRR($th, 'full_pipeline'),
                'apcer' => AttendanceLog::calculateAPCER(),
                'bpcer' => AttendanceLog::calculateBPCER(),
                'total' => AttendanceLog::query()->byMode('full_pipeline')->count(),
            ],
            'combined' => [
                'label' => 'Semua Data',
                'far'   => AttendanceLog::calculateFAR($th, null),
                'frr'   => AttendanceLog::calculateFRR($th, null),
                'apcer' => AttendanceLog::calculateAPCER(),
                'bpcer' => AttendanceLog::calculateBPCER(),
                'total' => AttendanceLog::count(),
            ],
        ];
    }

    /**
     * Statistik ringkasan untuk kartu atas.
     */
    public function getSummaryStats(): array
    {
        $th = $this->filterThreshold ? (float) $this->filterThreshold : null;
        $mode = $this->filterMode !== 'all' ? $this->filterMode : null;

        $query = AttendanceLog::query();
        if ($th)   $query->byThreshold($th);
        if ($mode) $query->byMode($mode);

        return [
            'total'    => $query->count(),
            'accepted' => (clone $query)->accepted()->count(),
            'rejected' => (clone $query)->rejected()->count(),
            'genuine'  => (clone $query)->genuine()->count(),
            'attack'   => (clone $query)->attack()->count(),
        ];
    }

    /**
     * Data untuk grafik FAR & FRR vs Threshold (Sistem A & B).
     * Mengambil semua nilai threshold unik yang pernah dipakai.
     */
    public function getChartData(): array
    {
        // Ambil semua threshold unik dari log
        $thresholds = AttendanceLog::select('threshold_used')
            ->distinct()
            ->orderBy('threshold_used')
            ->pluck('threshold_used')
            ->toArray();

        // Jika belum ada log, kembalikan array kosong — chart tampil kosong
        if (empty($thresholds)) {
            return ['labels' => [], 'far_a' => [], 'frr_a' => [], 'far_b' => [], 'frr_b' => [], 'apcer' => [], 'bpcer' => []];
        }

        $farA  = [];
        $frrA  = [];
        $farB  = [];
        $frrB  = [];
        $apcer = [];
        $bpcer = [];

        foreach ($thresholds as $th) {
            $farA[]  = AttendanceLog::calculateFAR($th, 'facenet_only');
            $frrA[]  = AttendanceLog::calculateFRR($th, 'facenet_only');
            $farB[]  = AttendanceLog::calculateFAR($th, 'full_pipeline');
            $frrB[]  = AttendanceLog::calculateFRR($th, 'full_pipeline');
            $apcer[] = AttendanceLog::calculateAPCER();
            $bpcer[] = AttendanceLog::calculateBPCER();
        }

        return [
            'labels' => array_map(fn($v) => number_format($v, 2), $thresholds),
            'far_a'  => $farA,
            'frr_a'  => $frrA,
            'far_b'  => $farB,
            'frr_b'  => $frrB,
            'apcer'  => $apcer,
            'bpcer'  => $bpcer,
        ];
    }

    /**
     * Data tabel perbandingan per threshold.
     */
    public function getComparisonTable(): array
    {
        $thresholds = AttendanceLog::select('threshold_used')
            ->distinct()
            ->orderBy('threshold_used')
            ->pluck('threshold_used')
            ->toArray();

        $rows = [];
        foreach ($thresholds as $th) {
            $rows[] = [
                'threshold' => number_format($th, 2),
                'far_a'     => AttendanceLog::calculateFAR($th, 'facenet_only'),
                'frr_a'     => AttendanceLog::calculateFRR($th, 'facenet_only'),
                'far_b'     => AttendanceLog::calculateFAR($th, 'full_pipeline'),
                'frr_b'     => AttendanceLog::calculateFRR($th, 'full_pipeline'),
                'apcer'     => AttendanceLog::calculateAPCER(),
                'bpcer'     => AttendanceLog::calculateBPCER(),
            ];
        }

        return $rows;
    }

    /**
     * Log terbaru untuk tabel preview.
     */
    public function getRecentLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return AttendanceLog::with('user')
            ->latest('attempted_at')
            ->take(15)
            ->get();
    }

    /**
     * Threshold aktif saat ini.
     */
    public function getActiveThresholds(): array
    {
        return [
            'facenet'  => Threshold::getActive()?->value ?? 0.75,
            'liveness' => LivenessThreshold::getActiveValue(),
        ];
    }
}
