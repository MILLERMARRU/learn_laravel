<?php

namespace Database\Seeders;

use App\Models\Almacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder
{
    public function run(): void
    {
        $almacenes = [
            [
                'nombre'      => 'Licorería Central',
                'descripcion' => 'Sede principal',
                'direccion'   => 'Av. Grau 123',
                'responsable' => 'Juan Pérez',
                'telefono'    => '999111222',
                'activo'      => true,
            ],
            [
                'nombre'      => 'Licorería San Juan',
                'descripcion' => 'Segunda sede',
                'direccion'   => 'Jr. Lima 456',
                'responsable' => 'María Torres',
                'telefono'    => '988333444',
                'activo'      => true,
            ],
            [
                'nombre'      => 'Depósito Central',
                'descripcion' => 'Stock sin atención al público',
                'direccion'   => 'Jr. Perú 456',
                'responsable' => 'Sam Vásquez',
                'telefono'    => '976453142',
                'activo'      => true,
            ],
        ];

        foreach ($almacenes as $data) {
            Almacen::create($data);
        }
    }
}
