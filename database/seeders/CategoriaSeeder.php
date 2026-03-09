<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Licores',     'descripcion' => 'Bebidas alcohólicas: ron, whisky, vodka, etc.'],
            ['nombre' => 'Gaseosas',    'descripcion' => 'Bebidas no alcohólicas carbonatadas'],
            ['nombre' => 'Snacks',      'descripcion' => 'Papas fritas, doritos, piqueos'],
            ['nombre' => 'Galletas',    'descripcion' => 'Galletas dulces y saladas'],
            ['nombre' => 'Hielo',       'descripcion' => 'Bolsas de hielo para consumo'],
            ['nombre' => 'Cigarrillos', 'descripcion' => 'Productos de tabaco'],
            ['nombre' => 'Otros',       'descripcion' => 'Productos varios'],
        ];

        foreach ($categorias as $data) {
            Categoria::create($data);
        }
    }
}
