<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            // ── Administradores ──────────────────────────────
            [
                'rol_id'               => 1, // Administrador
                'username'             => 'miller',
                'email'                => 'miller@licoreria.com',
                'password_hash'        => Hash::make('admin123'),
                'must_change_password' => false,
                'activo'               => true,
            ],
            [
                'rol_id'               => 1,
                'username'             => 'sam',
                'email'                => 'sam@licoreria.com',
                'password_hash'        => Hash::make('admin123'),
                'must_change_password' => false,
                'activo'               => true,
            ],
            // ── Vendedores ───────────────────────────────────
            [
                'rol_id'               => 2, // Vendedor
                'username'             => 'carlos',
                'email'                => 'carlos@licoreria.com',
                'password_hash'        => Hash::make('vendedor123'),
                'must_change_password' => false,
                'activo'               => true,
            ],
            [
                'rol_id'               => 2,
                'username'             => 'lucia',
                'email'                => 'lucia@licoreria.com',
                'password_hash'        => Hash::make('vendedor123'),
                'must_change_password' => false,
                'activo'               => true,
            ],
        ];

        foreach ($usuarios as $data) {
            Usuario::create($data);
        }
    }
}
