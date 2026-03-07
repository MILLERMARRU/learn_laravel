# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Servidor de desarrollo
php artisan serve                      # http://localhost:8000

# Migraciones
php artisan migrate                    # ejecuta migraciones pendientes
php artisan migrate:fresh              # borra todo y vuelve a migrar
php artisan migrate:fresh --seed       # + seeders

# Rutas registradas
php artisan route:list --path=api

# Linter
./vendor/bin/pint

# Tests
php artisan test
php artisan test --filter NombreTest

# Generar JWT secret (cuando se instale tymon/jwt-auth)
php artisan jwt:secret

# Producción
composer install --no-dev --optimize-autoloader
php artisan config:cache && php artisan route:cache && php artisan migrate --force
```

## Stack

- **Laravel 12** · PHP 8.2+
- **Eloquent** ORM con SoftDeletes donde aplique
- **MySQL** (Docker local) · SQLite `:memory:` en tests
- **JWT** `tymon/jwt-auth` — autenticación (pendiente de instalar)
- **Pest** — framework de tests
- **OpenAPI 3.0** — documentación en `docs/openapi.yaml`

## Arquitectura en capas

```
Request → Route → Controller → Service → Repository → Model → MySQL
```

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| Routes | `routes/api.php` | Define endpoints (`/api/v1/...`) |
| Controller | `app/Http/Controllers/Api/` | Recibe request, devuelve response JSON |
| Form Requests | `app/Http/Requests/` | Validación de entrada |
| API Resources | `app/Http/Resources/` | Formateo de respuesta JSON |
| Service | `app/Services/` | Lógica de negocio |
| Service Contracts | `app/Services/Contracts/` | Interfaces de servicios |
| Repository | `app/Repositories/` | Acceso a datos vía Eloquent |
| Repository Contracts | `app/Repositories/Contracts/` | Interfaces de repositorios |
| Model | `app/Models/` | Mapea tabla, define relaciones y casts |
| Provider | `app/Providers/RepositoryServiceProvider.php` | Bind Interface → Implementación |

**Principios SOLID aplicados:**
- **S** — Controller / Service / Repository / Model con responsabilidad única
- **O** — Interfaces permiten extender sin modificar clases existentes
- **D** — Controller inyecta ServiceInterface, Service inyecta RepositoryInterface (nunca clases concretas)
- Route Model Binding en todos los controllers — Laravel resuelve 404 automáticamente
- `destroy()` retorna **HTTP 204 No Content** siempre (sin body)
- `store()` retorna **HTTP 201 Created**
- Soft delete en módulos con historial (productos). Hard delete en módulos sin dependencias (categorias)

## Formato de respuesta estándar

```json
// Éxito
{ "success": true, "message": "Descripción", "data": { ... } }

// Error
{ "success": false, "message": "Descripción", "errors": { ... } }
```

El método `apiResponse()` en `app/Http/Controllers/Controller.php` genera este formato.
`destroy()` usa `response()->json(null, 204)` directamente (sin body).

## Base de datos

```
categorias                    productos
──────────                    ─────────
id                            id
nombre                        cod_producto (unique)
descripcion                   nombre
timestamps                    categoria_id (FK → categorias, restrictOnDelete)
                              marca (nullable)
                              unidad_medida
                              contenido (nullable)
                              precio_compra decimal(10,2)
                              precio_minorista decimal(10,2)
                              precio_mayorista decimal(10,2)
                              stock_minimo unsignedInteger
                              activo boolean default true
                              deleted_at (soft delete)
                              timestamps
```

**Relaciones Eloquent:**
- `Categoria` hasMany `Producto`
- `Producto` belongsTo `Categoria`

## Endpoints implementados

```
# Categorías (hard delete)
GET    /api/v1/categorias              ?search=
POST   /api/v1/categorias
GET    /api/v1/categorias/{id}
PUT    /api/v1/categorias/{id}
PATCH  /api/v1/categorias/{id}
DELETE /api/v1/categorias/{id}         → 204

# Productos (soft delete)
GET    /api/v1/productos               ?search= &categoria_id= &activo= &con_eliminados= &per_page=
POST   /api/v1/productos
GET    /api/v1/productos/{id}
PUT    /api/v1/productos/{id}
PATCH  /api/v1/productos/{id}
DELETE /api/v1/productos/{id}          → 204 (soft delete)
```

## Documentación Swagger

- **Spec:** `docs/openapi.yaml` — OpenAPI 3.0, archivo único, no mezcla con código
- **UI:** `http://localhost:8000/docs` — solo disponible con `APP_ENV=local`
- **Controller:** `app/Http/Controllers/SwaggerController.php`
- Swagger UI carga desde CDN (unpkg.com), sin paquetes PHP extra

## Bindings registrados (RepositoryServiceProvider)

```php
CategoriaRepositoryInterface → CategoriaRepository
CategoriaServiceInterface    → CategoriaService
ProductoRepositoryInterface  → ProductoRepository
ProductoServiceInterface     → ProductoService
```

Provider registrado en `bootstrap/app.php` via `withProviders()`.

## Orden de implementación

1. ✅ Categorías CRUD — refactorizado con SOLID completo
2. ✅ Productos CRUD — soft delete, filtros, relación con Categoria
3. ✅ Swagger UI — documentación interactiva en /docs
4. ⬜ Almacenes CRUD + pivot almacen_producto
5. ⬜ Auth JWT (login, register, logout, me)
6. ⬜ Proteger rutas con middleware `auth:api`
