<?php

namespace App\Providers;

use App\Repositories\CategoriaRepository;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Aquí se enlazan las interfaces con sus implementaciones concretas.
     * El contenedor de Laravel inyectará la implementación correcta
     * cada vez que se solicite la interfaz (Dependency Inversion).
     */
    public function register(): void
    {
        $this->app->bind(
            CategoriaRepositoryInterface::class,
            CategoriaRepository::class,
        );
    }
}
