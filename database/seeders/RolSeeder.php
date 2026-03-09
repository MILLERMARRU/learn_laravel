<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre'      => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
            ],
            [
                'nombre'      => 'Vendedor',
                'descripcion' => 'Acceso a ventas y consulta de inventario',
            ],
        ];

        foreach ($roles as $data) {
            Rol::create($data);
        }
    }
}
