<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de producción — datos mínimos para arrancar el sistema.
 *
 * Uso:
 *   php artisan db:seed --class=ProductionSeeder
 *
 * Es idempotente: usa firstOrCreate, se puede ejecutar múltiples
 * veces sin duplicar registros.
 *
 * Credenciales iniciales del admin:
 *   username : admin
 *   password : definida en ADMIN_PASSWORD (.env)
 *              fallback seguro: Admin@Licoreria2025!
 *
 * ⚠️  Cambia la contraseña en el primer login o vía PUT /api/v1/usuarios/{id}
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // ══ 1. ROLES BASE ════════════════════════════════════════════

        $administrador = Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso total al sistema.'],
        );

        Rol::firstOrCreate(
            ['nombre' => 'Vendedor'],
            ['descripcion' => 'Operaciones de venta e inventario de lectura.'],
        );

        // ══ 2. USUARIO ADMINISTRADOR INICIAL ═════════════════════════

        $passwordPlano = env('ADMIN_PASSWORD', 'Admin@Licoreria2025!');

        $usuario = Usuario::firstOrCreate(
            ['username' => 'admin'],
            [
                'rol_id'               => $administrador->id,
                'email'                => env('ADMIN_EMAIL', 'admin@licoreria.com'),
                'password_hash'        => Hash::make($passwordPlano),
                'must_change_password' => true,   // fuerza cambio en primer acceso
                'activo'               => true,
            ],
        );

        // Si el usuario ya existía, asegura que tenga rol Administrador y esté activo
        if (! $usuario->wasRecentlyCreated) {
            $usuario->update([
                'rol_id' => $administrador->id,
                'activo' => true,
            ]);
        }

        // ══ 3. REPORTE EN CONSOLA ═════════════════════════════════════

        $this->command->info('');
        $this->command->info('  ✔  ProductionSeeder ejecutado correctamente.');
        $this->command->info('');
        $this->command->table(
            ['Campo', 'Valor'],
            [
                ['Rol',      'Administrador'],
                ['Username', 'admin'],
                ['Email',    env('ADMIN_EMAIL', 'admin@licoreria.com')],
                ['Password', $passwordPlano],
                ['must_change_password', 'true — cambia en el primer acceso'],
            ],
        );
        $this->command->warn('  ⚠  Cambia la contraseña después del primer login.');
        $this->command->info('');
    }
}
