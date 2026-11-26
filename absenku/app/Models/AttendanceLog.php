<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'confidence_score',
        'liveness_score',
        'spoof_type',
        'threshold_used',
        'result',
        'rejection_reason',
        'capture_path',
        'is_actual_user',
        'attack_type',
        'evaluation_mode',
        'attempted_at',
        'checked_out_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'float',
            'liveness_score'   => 'float',
            'threshold_used'   => 'float',
            'is_actual_user'   => 'boolean',
            'attempted_at'     => 'datetime',
            'checked_out_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('result', 'accepted');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('result', 'rejected');
    }

    public function scopeByThreshold(Builder $query, float $threshold): Builder
    {
        return $query->where('threshold_used', $threshold);
    }

    /** Filter hanya percobaan genuine (attack_type = 'genuine') */
    public function scopeGenuine(Builder $query): Builder
    {
        return $query->where('attack_type', 'genuine');
    }

    /** Filter hanya percobaan serangan spoofing */
    public function scopeAttack(Builder $query): Builder
    {
        return $query->whereIn('attack_type', ['print_photo', 'digital_screen']);
    }

    /** Filter berdasarkan mode evaluasi */
    public function scopeByMode(Builder $query, string $mode): Builder
    {
        return $query->where('evaluation_mode', $mode);
    }

    // ── FAR & FRR ─────────────────────────────────────────────
    // Sumber label: kolom attack_type ('genuine' | 'print_photo' | 'digital_screen')
    // Berlaku untuk KEDUA sistem (Sistem A: facenet_only, Sistem B: full_pipeline)

    /**
     * FAR — False Acceptance Rate
     * Proporsi serangan spoofing yang berhasil lolos (diterima oleh sistem).
     *
     * FAR = attack diterima / total attack × 100
     *
     * @param float|null $threshold  Filter threshold FaceNet (null = semua)
     * @param string|null $mode      'full_pipeline' | 'facenet_only' | null (semua)
     */
    public static function calculateFAR(?float $threshold = null, ?string $mode = null): float
    {
        $query = static::attack();

        if ($threshold !== null) $query->byThreshold($threshold);
        if ($mode !== null)      $query->byMode($mode);

        $totalAttacks  = (clone $query)->count();
        $falseAccepted = (clone $query)->accepted()->count();

        if ($totalAttacks === 0) return 0.0;

        return round(($falseAccepted / $totalAttacks) * 100, 2);
    }

    /**
     * FRR — False Rejection Rate
     * Proporsi user genuine yang salah ditolak oleh sistem.
     *
     * FRR = genuine ditolak / total genuine × 100
     *
     * @param float|null $threshold  Filter threshold FaceNet (null = semua)
     * @param string|null $mode      'full_pipeline' | 'facenet_only' | null (semua)
     */
    public static function calculateFRR(?float $threshold = null, ?string $mode = null): float
    {
        $query = static::genuine();

        if ($threshold !== null) $query->byThreshold($threshold);
        if ($mode !== null)      $query->byMode($mode);

        $totalGenuines = (clone $query)->count();
        $falseRejected = (clone $query)->rejected()->count();

        if ($totalGenuines === 0) return 0.0;

        return round(($falseRejected / $totalGenuines) * 100, 2);
    }

    // ── APCER & BPCER ─────────────────────────────────────────
    // Metrik ISO/IEC 30107-3 khusus liveness detection (Sistem B: full_pipeline)
    // Hanya bermakna pada log dengan evaluation_mode = 'full_pipeline'

    /**
     * APCER — Attack Presentation Classification Error Rate
     * Proporsi serangan spoofing yang LOLOS liveness detection (dianggap Real).
     *
     * APCER = attack yang liveness_score >= liveness_threshold / total attack × 100
     *
     * Serangan "lolos liveness" ditandai dengan rejection_reason != 'spoof_detected'
     * (artinya liveness check tidak menangkap mereka).
     *
     * @param float|null $livenessThreshold  Filter threshold liveness (null = semua)
     */
    public static function calculateAPCER(?float $livenessThreshold = null): float
    {
        $query = static::attack()->byMode('full_pipeline');

        $totalAttacks = (clone $query)->count();

        // Serangan yang lolos liveness = diterima ATAU ditolak karena low_confidence
        // (bukan karena spoof_detected)
        $passedLiveness = (clone $query)
            ->where(function (Builder $q) {
                $q->where('rejection_reason', '!=', 'spoof_detected')
                  ->orWhereNull('rejection_reason');
            })
            ->count();

        if ($totalAttacks === 0) return 0.0;

        return round(($passedLiveness / $totalAttacks) * 100, 2);
    }

    /**
     * BPCER — Bona Fide Presentation Classification Error Rate
     * Proporsi wajah asli yang DITOLAK oleh liveness detection (dianggap Spoof).
     *
     * BPCER = genuine yang rejection_reason = 'spoof_detected' / total genuine × 100
     */
    public static function calculateBPCER(): float
    {
        $totalGenuines = static::genuine()->byMode('full_pipeline')->count();

        $rejectedByLiveness = static::genuine()
            ->byMode('full_pipeline')
            ->where('rejection_reason', 'spoof_detected')
            ->count();

        if ($totalGenuines === 0) return 0.0;

        return round(($rejectedByLiveness / $totalGenuines) * 100, 2);
    }

    /**
     * Hitung semua metrik sekaligus untuk dashboard.
     * Mengembalikan array ringkasan per mode evaluasi.
     *
     * @param float|null $threshold  Filter threshold FaceNet
     * @param string|null $mode      'full_pipeline' | 'facenet_only' | null (semua)
     */
    public static function calculateAllMetrics(?float $threshold = null, ?string $mode = null): array
    {
        return [
            // FAR & FRR: berlaku untuk kedua sistem
            'far'  => static::calculateFAR($threshold, $mode),
            'frr'  => static::calculateFRR($threshold, $mode),

            // APCER & BPCER: hanya relevan untuk full_pipeline (Sistem B)
            'apcer' => ($mode === null || $mode === 'full_pipeline')
                ? static::calculateAPCER()
                : null,
            'bpcer' => ($mode === null || $mode === 'full_pipeline')
                ? static::calculateBPCER()
                : null,

            // Statistik pendukung
            'total_attempts'  => static::when($threshold, fn($q) => $q->byThreshold($threshold))
                                        ->when($mode, fn($q) => $q->byMode($mode))
                                        ->count(),
            'total_accepted'  => static::accepted()
                                        ->when($threshold, fn($q) => $q->byThreshold($threshold))
                                        ->when($mode, fn($q) => $q->byMode($mode))
                                        ->count(),
            'total_rejected'  => static::rejected()
                                        ->when($threshold, fn($q) => $q->byThreshold($threshold))
                                        ->when($mode, fn($q) => $q->byMode($mode))
                                        ->count(),
            'total_genuine'   => static::genuine()
                                        ->when($threshold, fn($q) => $q->byThreshold($threshold))
                                        ->when($mode, fn($q) => $q->byMode($mode))
                                        ->count(),
            'total_attack'    => static::attack()
                                        ->when($threshold, fn($q) => $q->byThreshold($threshold))
                                        ->when($mode, fn($q) => $q->byMode($mode))
                                        ->count(),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isAccepted(): bool
    {
        return $this->result === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->result === 'rejected';
    }

    public function isGenuine(): bool
    {
        return $this->attack_type === 'genuine';
    }

    public function isAttack(): bool
    {
        return in_array($this->attack_type, ['print_photo', 'digital_screen']);
    }

    public function getCaptureUrlAttribute(): ?string
    {
        return $this->capture_path
            ? asset('storage/' . $this->capture_path)
            : null;
    }
}