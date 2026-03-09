<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
    // Solo created_at, sin updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function estaVencido(): bool
    {
        return $this->expires_at->isPast();
    }
}
