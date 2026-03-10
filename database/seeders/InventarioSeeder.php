<?php

namespace Database\Seeders;

use App\Models\Inventario;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        // Stock inicial para los 25 productos en los 3 almacenes
        // Almacén 1 = Licorería Central (id=1)
        // Almacén 2 = Licorería San Juan (id=2)
        // Almacén 3 = Depósito Central   (id=3)

        $stocks = [
            // producto_id => [almacen1, almacen2, almacen3]
             1 => [80, 50, 150],  // Ron Cartavio
             2 => [60, 40, 120],  // Whisky JW Red
             3 => [50, 30, 100],  // Vodka Absolut
             4 => [70, 45, 140],  // Ron Bacardí
             5 => [65, 40, 130],  // Pisco Queirolo
             6 => [100, 60, 200], // Inca Kola
             7 => [100, 60, 200], // Coca Cola
             8 => [90, 55, 180],  // Sprite
             9 => [90, 55, 180],  // Fanta
            10 => [100, 60, 200], // Pepsi
            11 => [120, 70, 250], // Papas Lay's
            12 => [120, 70, 250], // Doritos
            13 => [120, 70, 250], // Chizitos
            14 => [120, 70, 250], // Cuates
            15 => [100, 60, 200], // Oreo
            16 => [100, 60, 200], // Galleta Vainilla
            17 => [100, 60, 200], // Soda Victoria
            18 => [80, 50, 150],  // Hielo 3kg
            19 => [80, 50, 150],  // Hielo 5kg
            20 => [90, 55, 180],  // Marlboro Red
            21 => [90, 55, 180],  // Lucky Strike
            22 => [90, 55, 180],  // Hamilton Azul
            23 => [150, 80, 300], // Agua San Luis
            24 => [80, 50, 150],  // Red Bull
            25 => [100, 60, 200], // Jugo Frugos
            26 => [100, 60, 200], // Rellenita
        ];

        foreach ($stocks as $productoId => [$cant1, $cant2, $cant3]) {
            Inventario::create([
                'producto_id'         => $productoId,
                'almacen_id'          => 1,
                'cantidad'            => $cant1,
                'cantidad_reservada'  => 0,
                'cantidad_minima'     => 5,
                'ultima_actualizacion' => now(),
            ]);

            Inventario::create([
                'producto_id'         => $productoId,
                'almacen_id'          => 2,
                'cantidad'            => $cant2,
                'cantidad_reservada'  => 0,
                'cantidad_minima'     => 5,
                'ultima_actualizacion' => now(),
            ]);

            Inventario::create([
                'producto_id'         => $productoId,
                'almacen_id'          => 3,
                'cantidad'            => $cant3,
                'cantidad_reservada'  => 0,
                'cantidad_minima'     => 20,
                'ultima_actualizacion' => now(),
            ]);
        }
    }
}
