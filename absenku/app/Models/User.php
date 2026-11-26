<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'identity_number',
        'name',
        'email',
        'password',
        'department',
        'role',
        'face_registered',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'face_registered'   => 'boolean',
        ];
    }
    // -- fillament ----------------------
     public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
    // ── Helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // ── Relationships ─────────────────────────────────────────

    public function faceEmbeddings(): HasMany
    {
        return $this->hasMany(FaceEmbedding::class);
    }

    public function activeFaceEmbeddings(): HasMany
    {
        return $this->hasMany(FaceEmbedding::class)->where('is_active', true);
    }

    public function faceSamples(): HasMany
    {
        return $this->hasMany(FaceSample::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function thresholdsSet(): HasMany
    {
        return $this->hasMany(Threshold::class, 'set_by');
    }
}
