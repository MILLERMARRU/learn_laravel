<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    use HasFactory;

    // Sin SoftDeletes — hard delete correcto:
    // esta tabla no es FK en otras tablas y representa estado actual,
    // no historial (el historial vive en movimientos).
    protected $table = 'inventario';

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'cantidad',
        'cantidad_reservada',
        'cantidad_minima',
        'ultima_actualizacion',
    ];

    protected $casts = [
        'cantidad'           => 'integer',
        'cantidad_reservada' => 'integer',
        'cantidad_minima'    => 'integer',
        'ultima_actualizacion' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }
}
