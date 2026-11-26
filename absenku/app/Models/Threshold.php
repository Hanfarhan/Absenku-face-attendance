<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Threshold extends Model
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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }

    public function setAsActive(): void
    {
        static::where('is_active', true)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
