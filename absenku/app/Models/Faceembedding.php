<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceEmbedding extends Model
{
    protected $fillable = [
        'user_id',
        'embedding',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function deactivateAllForUser(int $userId): void
    {
        static::where('user_id', $userId)
              ->where('is_active', true)
              ->update(['is_active' => false]);
    }
}
