<?php

namespace Database\Seeders;

use App\Models\Movimiento;
use Illuminate\Database\Seeder;

class MovimientoSeeder extends Seeder
{
    public function run(): void
    {
        // Movimientos de ENTRADA que justifican el stock inicial del InventarioSeeder.
        // usuario_id=1 (miller, administrador) registra la carga en los 3 almacenes.

        $entradas = [

            // ── Almacén 1 — Licorería Central ─────────────────
            // Licores
            ['producto_id' =>  1, 'almacen_id' => 1, 'cantidad' => 80,  'descripcion' => 'Carga inicial — Ron Cartavio'],
            ['producto_id' =>  2, 'almacen_id' => 1, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Whisky JW Red'],
            ['producto_id' =>  3, 'almacen_id' => 1, 'cantidad' => 50,  'descripcion' => 'Carga inicial — Vodka Absolut'],
            ['producto_id' =>  4, 'almacen_id' => 1, 'cantidad' => 70,  'descripcion' => 'Carga inicial — Ron Bacardí'],
            ['producto_id' =>  5, 'almacen_id' => 1, 'cantidad' => 65,  'descripcion' => 'Carga inicial — Pisco Queirolo'],
            // Gaseosas
            ['producto_id' =>  6, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Inca Kola'],
            ['producto_id' =>  7, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Coca Cola'],
            ['producto_id' =>  8, 'almacen_id' => 1, 'cantidad' => 90,  'descripcion' => 'Carga inicial — Sprite'],
            ['producto_id' =>  9, 'almacen_id' => 1, 'cantidad' => 90,  'descripcion' => 'Carga inicial — Fanta'],
            ['producto_id' => 10, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Pepsi'],
            // Snacks
            ['producto_id' => 11, 'almacen_id' => 1, 'cantidad' => 120, 'descripcion' => "Carga inicial — Papas Lay's"],
            ['producto_id' => 12, 'almacen_id' => 1, 'cantidad' => 120, 'descripcion' => 'Carga inicial — Doritos'],
            ['producto_id' => 13, 'almacen_id' => 1, 'cantidad' => 120, 'descripcion' => 'Carga inicial — Chizitos'],
            ['producto_id' => 14, 'almacen_id' => 1, 'cantidad' => 120, 'descripcion' => 'Carga inicial — Cuates'],
            // Galletas
            ['producto_id' => 15, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Oreo'],
            ['producto_id' => 16, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Galleta Vainilla'],
            ['producto_id' => 17, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Soda Victoria'],
            // Hielo
            ['producto_id' => 18, 'almacen_id' => 1, 'cantidad' => 80,  'descripcion' => 'Carga inicial — Hielo 3kg'],
            ['producto_id' => 19, 'almacen_id' => 1, 'cantidad' => 80,  'descripcion' => 'Carga inicial — Hielo 5kg'],
            // Cigarrillos
            ['producto_id' => 20, 'almacen_id' => 1, 'cantidad' => 90,  'descripcion' => 'Carga inicial — Marlboro Red'],
            ['producto_id' => 21, 'almacen_id' => 1, 'cantidad' => 90,  'descripcion' => 'Carga inicial — Lucky Strike'],
            ['producto_id' => 22, 'almacen_id' => 1, 'cantidad' => 90,  'descripcion' => 'Carga inicial — Hamilton Azul'],
            // Otros
            ['producto_id' => 23, 'almacen_id' => 1, 'cantidad' => 150, 'descripcion' => 'Carga inicial — Agua San Luis'],
            ['producto_id' => 24, 'almacen_id' => 1, 'cantidad' => 80,  'descripcion' => 'Carga inicial — Red Bull'],
            ['producto_id' => 25, 'almacen_id' => 1, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Jugo Frugos'],

            // ── Almacén 2 — Licorería San Juan ────────────────
            ['producto_id' =>  1, 'almacen_id' => 2, 'cantidad' => 50,  'descripcion' => 'Carga inicial — Ron Cartavio'],
            ['producto_id' =>  2, 'almacen_id' => 2, 'cantidad' => 40,  'descripcion' => 'Carga inicial — Whisky JW Red'],
            ['producto_id' =>  3, 'almacen_id' => 2, 'cantidad' => 30,  'descripcion' => 'Carga inicial — Vodka Absolut'],
            ['producto_id' =>  4, 'almacen_id' => 2, 'cantidad' => 45,  'descripcion' => 'Carga inicial — Ron Bacardí'],
            ['producto_id' =>  5, 'almacen_id' => 2, 'cantidad' => 40,  'descripcion' => 'Carga inicial — Pisco Queirolo'],
            ['producto_id' =>  6, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Inca Kola'],
            ['producto_id' =>  7, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Coca Cola'],
            ['producto_id' =>  8, 'almacen_id' => 2, 'cantidad' => 55,  'descripcion' => 'Carga inicial — Sprite'],
            ['producto_id' =>  9, 'almacen_id' => 2, 'cantidad' => 55,  'descripcion' => 'Carga inicial — Fanta'],
            ['producto_id' => 10, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Pepsi'],
            ['producto_id' => 11, 'almacen_id' => 2, 'cantidad' => 70,  'descripcion' => "Carga inicial — Papas Lay's"],
            ['producto_id' => 12, 'almacen_id' => 2, 'cantidad' => 70,  'descripcion' => 'Carga inicial — Doritos'],
            ['producto_id' => 13, 'almacen_id' => 2, 'cantidad' => 70,  'descripcion' => 'Carga inicial — Chizitos'],
            ['producto_id' => 14, 'almacen_id' => 2, 'cantidad' => 70,  'descripcion' => 'Carga inicial — Cuates'],
            ['producto_id' => 15, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Oreo'],
            ['producto_id' => 16, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Galleta Vainilla'],
            ['producto_id' => 17, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Soda Victoria'],
            ['producto_id' => 18, 'almacen_id' => 2, 'cantidad' => 50,  'descripcion' => 'Carga inicial — Hielo 3kg'],
            ['producto_id' => 19, 'almacen_id' => 2, 'cantidad' => 50,  'descripcion' => 'Carga inicial — Hielo 5kg'],
            ['producto_id' => 20, 'almacen_id' => 2, 'cantidad' => 55,  'descripcion' => 'Carga inicial — Marlboro Red'],
            ['producto_id' => 21, 'almacen_id' => 2, 'cantidad' => 55,  'descripcion' => 'Carga inicial — Lucky Strike'],
            ['producto_id' => 22, 'almacen_id' => 2, 'cantidad' => 55,  'descripcion' => 'Carga inicial — Hamilton Azul'],
            ['producto_id' => 23, 'almacen_id' => 2, 'cantidad' => 80,  'descripcion' => 'Carga inicial — Agua San Luis'],
            ['producto_id' => 24, 'almacen_id' => 2, 'cantidad' => 50,  'descripcion' => 'Carga inicial — Red Bull'],
            ['producto_id' => 25, 'almacen_id' => 2, 'cantidad' => 60,  'descripcion' => 'Carga inicial — Jugo Frugos'],

            // ── Almacén 3 — Depósito Central ──────────────────
            ['producto_id' =>  1, 'almacen_id' => 3, 'cantidad' => 150, 'descripcion' => 'Carga inicial — Ron Cartavio'],
            ['producto_id' =>  2, 'almacen_id' => 3, 'cantidad' => 120, 'descripcion' => 'Carga inicial — Whisky JW Red'],
            ['producto_id' =>  3, 'almacen_id' => 3, 'cantidad' => 100, 'descripcion' => 'Carga inicial — Vodka Absolut'],
            ['producto_id' =>  4, 'almacen_id' => 3, 'cantidad' => 140, 'descripcion' => 'Carga inicial — Ron Bacardí'],
            ['producto_id' =>  5, 'almacen_id' => 3, 'cantidad' => 130, 'descripcion' => 'Carga inicial — Pisco Queirolo'],
            ['producto_id' =>  6, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Inca Kola'],
            ['producto_id' =>  7, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Coca Cola'],
            ['producto_id' =>  8, 'almacen_id' => 3, 'cantidad' => 180, 'descripcion' => 'Carga inicial — Sprite'],
            ['producto_id' =>  9, 'almacen_id' => 3, 'cantidad' => 180, 'descripcion' => 'Carga inicial — Fanta'],
            ['producto_id' => 10, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Pepsi'],
            ['producto_id' => 11, 'almacen_id' => 3, 'cantidad' => 250, 'descripcion' => "Carga inicial — Papas Lay's"],
            ['producto_id' => 12, 'almacen_id' => 3, 'cantidad' => 250, 'descripcion' => 'Carga inicial — Doritos'],
            ['producto_id' => 13, 'almacen_id' => 3, 'cantidad' => 250, 'descripcion' => 'Carga inicial — Chizitos'],
            ['producto_id' => 14, 'almacen_id' => 3, 'cantidad' => 250, 'descripcion' => 'Carga inicial — Cuates'],
            ['producto_id' => 15, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Oreo'],
            ['producto_id' => 16, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Galleta Vainilla'],
            ['producto_id' => 17, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Soda Victoria'],
            ['producto_id' => 18, 'almacen_id' => 3, 'cantidad' => 150, 'descripcion' => 'Carga inicial — Hielo 3kg'],
            ['producto_id' => 19, 'almacen_id' => 3, 'cantidad' => 150, 'descripcion' => 'Carga inicial — Hielo 5kg'],
            ['producto_id' => 20, 'almacen_id' => 3, 'cantidad' => 180, 'descripcion' => 'Carga inicial — Marlboro Red'],
            ['producto_id' => 21, 'almacen_id' => 3, 'cantidad' => 180, 'descripcion' => 'Carga inicial — Lucky Strike'],
            ['producto_id' => 22, 'almacen_id' => 3, 'cantidad' => 180, 'descripcion' => 'Carga inicial — Hamilton Azul'],
            ['producto_id' => 23, 'almacen_id' => 3, 'cantidad' => 300, 'descripcion' => 'Carga inicial — Agua San Luis'],
            ['producto_id' => 24, 'almacen_id' => 3, 'cantidad' => 150, 'descripcion' => 'Carga inicial — Red Bull'],
            ['producto_id' => 25, 'almacen_id' => 3, 'cantidad' => 200, 'descripcion' => 'Carga inicial — Jugo Frugos'],
        ];

        foreach ($entradas as $data) {
            Movimiento::create(array_merge($data, [
                'usuario_id' => 1, // miller (administrador)
                'tipo'       => 'entrada',
                'fecha'      => '2026-03-01',
            ]));
        }
    }
}
