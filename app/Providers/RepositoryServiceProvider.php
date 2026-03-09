<?php

namespace App\Providers;

use App\Repositories\AlmacenRepository;
use App\Repositories\DetalleVentaRepository;
use App\Repositories\MovimientoRepository;
use App\Repositories\CategoriaRepository;
use App\Repositories\InventarioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\RolRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\VentaRepository;
use App\Repositories\Contracts\AlmacenRepositoryInterface;
use App\Repositories\Contracts\DetalleVentaRepositoryInterface;
use App\Repositories\Contracts\MovimientoRepositoryInterface;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use App\Repositories\Contracts\ProductoRepositoryInterface;
use App\Repositories\Contracts\RolRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Contracts\VentaRepositoryInterface;
use App\Services\AlmacenService;
use App\Services\AuthService;
use App\Services\DetalleVentaService;
use App\Services\MovimientoService;
use App\Services\CategoriaService;
use App\Services\InventarioService;
use App\Services\ProductoService;
use App\Services\RolService;
use App\Services\UsuarioService;
use App\Services\VentaService;
use App\Services\Contracts\AlmacenServiceInterface;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\DetalleVentaServiceInterface;
use App\Services\Contracts\MovimientoServiceInterface;
use App\Services\Contracts\CategoriaServiceInterface;
use App\Services\Contracts\InventarioServiceInterface;
use App\Services\Contracts\ProductoServiceInterface;
use App\Services\Contracts\RolServiceInterface;
use App\Services\Contracts\UsuarioServiceInterface;
use App\Services\Contracts\VentaServiceInterface;
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

        $this->app->bind(
            RolRepositoryInterface::class,
            RolRepository::class,
        );

        $this->app->bind(
            RolServiceInterface::class,
            RolService::class,
        );

        $this->app->bind(
            UsuarioRepositoryInterface::class,
            UsuarioRepository::class,
        );

        $this->app->bind(
            UsuarioServiceInterface::class,
            UsuarioService::class,
        );

        $this->app->bind(
            VentaRepositoryInterface::class,
            VentaRepository::class,
        );

        $this->app->bind(
            VentaServiceInterface::class,
            VentaService::class,
        );

        $this->app->bind(
            MovimientoRepositoryInterface::class,
            MovimientoRepository::class,
        );

        $this->app->bind(
            MovimientoServiceInterface::class,
            MovimientoService::class,
        );

        $this->app->bind(
            DetalleVentaRepositoryInterface::class,
            DetalleVentaRepository::class,
        );

        $this->app->bind(
            DetalleVentaServiceInterface::class,
            DetalleVentaService::class,
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class,
        );
    }
}
