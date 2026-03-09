<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Model implements Authenticatable, JWTSubject
{
    use HasFactory, SoftDeletes, AuthenticatableTrait;

    protected $fillable = [
        'rol_id',
        'username',
        'email',
        'password_hash',
        'must_change_password',
        'activo',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $attributes = [
        'activo'               => true,
        'must_change_password' => false,
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
        'activo'               => 'boolean',
        'ultimo_acceso'        => 'datetime',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    // ── JWTSubject ───────────────────────────────────────────────

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'rol' => $this->rol?->nombre,
        ];
    }

    // ── Authenticatable — campo de contraseña ────────────────────

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
