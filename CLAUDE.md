# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Desarrollo (servidor + queue worker + Vite en paralelo)
composer run dev

# Suite completa de tests
composer run test

# Test individual
php artisan test --filter NombreTest

# Setup inicial
composer run setup

# Linter
./vendor/bin/pint

# Migraciones
php artisan migrate
php artisan migrate:fresh          # borra todo y vuelve a migrar
php artisan migrate:fresh --seed   # + seeders

# Generar JWT secret (cuando se instale tymon/jwt-auth)
php artisan jwt:secret

# Producción
composer install --no-dev --optimize-autoloader
php artisan config:cache && php artisan route:cache && php artisan migrate --force
```

## Stack

- **Laravel 12** · PHP 8.2+
- **Eloquent** ORM
- **MySQL** (Docker local) · SQLite `:memory:` en tests
- **JWT** `tymon/jwt-auth` — autenticación (aún no instalado)
- **Pest 4.4** — framework de tests
- **Vite 7 + Tailwind CSS 4** — frontend (secundario, app es API-first)

## Arquitectura en capas

```
Request → Route → Controller → Service → Repository → Model → MySQL
```

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| Routes | `routes/api.php` | Define endpoints (`/api/v1/...`) |
| Controller | `app/Http/Controllers/Api/` | Recibe request, devuelve response JSON |
| DTO Request | `app/Http/Controllers/dto/request/` | Form Requests — validación |
| DTO Response | `app/Http/Controllers/dto/response/` | API Resources — formateo JSON |
| Service | `app/Services/` | Lógica de negocio, orquesta repositorios |
| Repository | `app/Repositories/` | Acceso a datos vía Eloquent |
| Contracts | `app/Repositories/Contracts/` | Interfaces — Dependency Inversion |
| Model | `app/Models/` | Mapea tabla, define relaciones |
| Provider | `app/Providers/RepositoryServiceProvider.php` | Bind Interface → Implementación |

**Principios aplicados:**
- Controller no toca la DB, Service no conoce HTTP, Repository no conoce negocio
- Inyección de interfaces, no clases concretas (Dependency Inversion)
- Hard delete (sin SoftDeletes) a menos que se indique lo contrario

## Formato de respuesta estándar

```json
// Éxito
{ "success": true, "data": { ... }, "message": "Descripción" }

// Error
{ "success": false, "message": "Descripción", "errors": { ... } }
```

El método `apiResponse()` en `app/Http/Controllers/Controller.php` genera este formato.

## Base de datos

```
categorias          productos              almacenes
──────────          ─────────              ─────────
id                  id                     id
nombre              nombre                 nombre
descripcion         descripcion            ubicacion
timestamps          precio                 timestamps
                    stock
                    categoria_id (FK)
                    timestamps

almacen_producto (pivot)
─────────────────────────
almacen_id · producto_id · cantidad · timestamps
```

**Relaciones Eloquent:**
- `Categoria` hasMany `Producto`
- `Producto` belongsTo `Categoria`
- `Producto` belongsToMany `Almacen` (pivot `almacen_producto`)

## Endpoints planeados

```
POST   /api/v1/auth/login          público
POST   /api/v1/auth/register       público
POST   /api/v1/auth/logout         [auth]
GET    /api/v1/auth/me             [auth]

GET|POST            /api/v1/categorias
GET|PUT|DELETE      /api/v1/categorias/{id}

GET|POST            /api/v1/productos
GET|PUT|DELETE      /api/v1/productos/{id}

GET|POST            /api/v1/almacenes
GET|PUT|DELETE      /api/v1/almacenes/{id}
POST                /api/v1/almacenes/{id}/productos  ← asignar producto
```

## Query params soportados (GET listados)

| Param | Descripción | Ejemplo |
|-------|-------------|---------|
| `search` | Filtra por nombre | `?search=ropa` |
| `per_page` | Resultados por página | `?per_page=10` |

## Orden de implementación

1. ✅ Categorias CRUD (sin JWT) — primera iteración
2. Productos CRUD (con relación a Categoria)
3. Almacenes CRUD + pivot almacen_producto
4. Auth JWT (login, register, logout, me)
5. Proteger rutas con middleware `auth:api`
