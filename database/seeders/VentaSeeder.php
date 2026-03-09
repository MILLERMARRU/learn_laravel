<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        // Ventas completadas con sus detalles y movimientos de salida.
        // Venta 4 queda en estado "pendiente" sin detalles para probar el flujo.

        $ventas = [

            // ── Venta 1 — Licorería Central (almacen=1), vendedor carlos (usuario=3) ──
            [
                'venta' => [
                    'usuario_id'         => 3, // carlos
                    'almacen_id'         => 1, // Licorería Central
                    'fecha'              => '2026-03-01',
                    'cliente'            => 'Pedro García',
                    'numero_comprobante' => 'F001-000001',
                    'tipo_pago'          => 'efectivo',
                    'estado'             => 'completada',
                    'activo'             => true,
                ],
                'detalles' => [
                    ['producto_id' => 1,  'cantidad' => 2, 'precio_unitario' => 28.00], // Ron Cartavio
                    ['producto_id' => 6,  'cantidad' => 6, 'precio_unitario' =>  4.00], // Inca Kola
                    ['producto_id' => 11, 'cantidad' => 5, 'precio_unitario' =>  2.50], // Papas Lay's
                ],
            ],

            // ── Venta 2 — Licorería San Juan (almacen=2), vendedora lucia (usuario=4) ──
            [
                'venta' => [
                    'usuario_id'         => 4, // lucia
                    'almacen_id'         => 2, // Licorería San Juan
                    'fecha'              => '2026-03-02',
                    'cliente'            => 'Ana Rodríguez',
                    'numero_comprobante' => 'F002-000001',
                    'tipo_pago'          => 'tarjeta',
                    'estado'             => 'completada',
                    'activo'             => true,
                ],
                'detalles' => [
                    ['producto_id' =>  2, 'cantidad' => 1, 'precio_unitario' => 70.00], // Whisky JW Red
                    ['producto_id' =>  7, 'cantidad' => 3, 'precio_unitario' =>  4.00], // Coca Cola
                    ['producto_id' => 15, 'cantidad' => 4, 'precio_unitario' =>  3.50], // Oreo
                ],
            ],

            // ── Venta 3 — Licorería Central (almacen=1), vendedor carlos (usuario=3) ──
            [
                'venta' => [
                    'usuario_id'         => 3,
                    'almacen_id'         => 1,
                    'fecha'              => '2026-03-05',
                    'cliente'            => 'Luis Mamani',
                    'numero_comprobante' => 'F001-000002',
                    'tipo_pago'          => 'efectivo',
                    'estado'             => 'completada',
                    'activo'             => true,
                ],
                'detalles' => [
                    ['producto_id' => 20, 'cantidad' => 3, 'precio_unitario' => 12.00], // Marlboro Red
                    ['producto_id' => 24, 'cantidad' => 2, 'precio_unitario' =>  7.00], // Red Bull
                    ['producto_id' => 18, 'cantidad' => 4, 'precio_unitario' =>  5.00], // Hielo 3kg
                ],
            ],
        ];

        foreach ($ventas as $item) {
            DB::transaction(function () use ($item) {
                // 1. Crear la venta con total=0 inicialmente
                $venta = Venta::create(array_merge($item['venta'], ['total' => 0]));

                $total = 0;

                foreach ($item['detalles'] as $detData) {
                    $productoId    = $detData['producto_id'];
                    $almacenId     = $venta->almacen_id;
                    $cantidad      = $detData['cantidad'];
                    $precioUnit    = $detData['precio_unitario'];
                    $subTotal      = $cantidad * $precioUnit;

                    // 2. Descontar del inventario
                    $inventario = Inventario::where('producto_id', $productoId)
                        ->where('almacen_id', $almacenId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $inventario->cantidad             -= $cantidad;
                    $inventario->ultima_actualizacion  = now();
                    $inventario->save();

                    // 3. Registrar movimiento de salida
                    $movimiento = Movimiento::create([
                        'producto_id' => $productoId,
                        'almacen_id'  => $almacenId,
                        'usuario_id'  => $venta->usuario_id,
                        'tipo'        => 'salida',
                        'cantidad'    => $cantidad,
                        'fecha'       => $venta->fecha,
                        'descripcion' => "Venta #{$venta->id} — comprobante {$venta->numero_comprobante}",
                    ]);

                    // 4. Crear detalle de venta
                    DetalleVenta::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $productoId,
                        'almacen_id'      => $almacenId,
                        'movimiento_id'   => $movimiento->id,
                        'cantidad'        => $cantidad,
                        'precio_unitario' => $precioUnit,
                        'sub_total'       => $subTotal,
                    ]);

                    $total += $subTotal;
                }

                // 5. Actualizar el total de la venta
                $venta->total = $total;
                $venta->save();
            });
        }

        // ── Venta 4: pendiente sin detalles (para probar el flujo manualmente) ──
        Venta::create([
            'usuario_id'         => 3,
            'almacen_id'         => 1,
            'fecha'              => '2026-03-08',
            'cliente'            => 'Rosa Quispe',
            'total'              => 0.00,
            'numero_comprobante' => 'F001-000003',
            'tipo_pago'          => 'efectivo',
            'estado'             => 'pendiente',
            'activo'             => true,
        ]);
    }
}
