<?php

namespace App\Providers;

use App\Repositories\CategoriaRepository;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use App\Repositories\ProductoRepository;
use App\Repositories\Contracts\ProductoRepositoryInterface;
use App\Services\CategoriaService;
use App\Services\Contracts\CategoriaServiceInterface;
use App\Services\ProductoService;
use App\Services\Contracts\ProductoServiceInterface;
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

        $this->app->bind(
            CategoriaServiceInterface::class,
            CategoriaService::class,
        );

        $this->app->bind(
            ProductoRepositoryInterface::class,
            ProductoRepository::class,
        );

        $this->app->bind(
            ProductoServiceInterface::class,
            ProductoService::class,
        );
    }
}
