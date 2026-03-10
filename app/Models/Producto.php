<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cod_producto',
        'codigo_barras',
        'nombre',
        'categoria_id',
        'marca',
        'unidad_medida',
        'contenido',
        'precio_compra',
        'precio_minorista',
        'precio_mayorista',
        'stock_minimo',
        'activo',
    ];

    protected $attributes = [
        'activo' => true,
    ];

    protected $casts = [
        'precio_compra'     => 'decimal:2',
        'precio_minorista'  => 'decimal:2',
        'precio_mayorista'  => 'decimal:2',
        'stock_minimo'      => 'integer',
        'activo'            => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
