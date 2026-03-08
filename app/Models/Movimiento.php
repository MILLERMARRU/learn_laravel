<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    use HasFactory;

    // Tabla de auditoría: solo created_at, sin updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'fecha',
        'descripcion',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'cantidad' => 'decimal:2',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
