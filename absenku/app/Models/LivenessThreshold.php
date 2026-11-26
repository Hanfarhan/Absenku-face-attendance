<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivenessThreshold extends Model
{
    protected $fillable = [
        'value',
        'set_by',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'     => 'float',
            'is_active' => 'boolean',
        ];
    }

    /** Relasi ke admin yang menetapkan threshold ini */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Ambil record liveness threshold yang sedang aktif.
     * Mengembalikan null jika belum ada threshold aktif.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }

    /**
     * Ambil nilai threshold aktif saja (float).
     * Fallback ke 0.5 jika belum ada record aktif.
     */
    public static function getActiveValue(): float
    {
        return static::getActive()?->value ?? 0.5;
    }

    /**
     * Set record ini sebagai aktif.
     * Otomatis menonaktifkan semua record lain.
     */
    public function setAsActive(): void
    {
        static::where('is_active', true)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
