<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolSeeder::class,       // 1. roles (sin FK)
            CategoriaSeeder::class, // 2. categorias (sin FK)
            AlmacenSeeder::class,   // 3. almacenes (sin FK)
            UsuarioSeeder::class,   // 4. usuarios (FK → roles)
            ProductoSeeder::class,  // 5. productos (FK → categorias)
            InventarioSeeder::class,  // 6. inventario (FK → productos, almacenes)
            MovimientoSeeder::class,  // 7. movimientos de entrada — carga inicial de stock
            VentaSeeder::class,       // 8. ventas + detalles + movimientos de salida
        ]);
    }
}
