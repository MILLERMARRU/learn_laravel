<?php

namespace App\Providers;

use App\Repositories\AlmacenRepository;
use App\Repositories\CategoriaRepository;
use App\Repositories\InventarioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\Contracts\AlmacenRepositoryInterface;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use App\Repositories\Contracts\ProductoRepositoryInterface;
use App\Services\AlmacenService;
use App\Services\CategoriaService;
use App\Services\InventarioService;
use App\Services\ProductoService;
use App\Services\Contracts\AlmacenServiceInterface;
use App\Services\Contracts\CategoriaServiceInterface;
use App\Services\Contracts\InventarioServiceInterface;
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

        $this->app->bind(
            AlmacenRepositoryInterface::class,
            AlmacenRepository::class,
        );

        $this->app->bind(
            AlmacenServiceInterface::class,
            AlmacenService::class,
        );

        $this->app->bind(
            InventarioRepositoryInterface::class,
            InventarioRepository::class,
        );

        $this->app->bind(
            InventarioServiceInterface::class,
            InventarioService::class,
        );
    }
}
