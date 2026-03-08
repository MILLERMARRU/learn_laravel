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
- Soft delete en módulos con historial. Hard delete en módulos sin historial

## Formato de respuesta estándar

```json
// Éxito
{ "success": true, "message": "Descripción", "data": { ... } }

// Error
{ "success": false, "message": "Descripción", "errors": { ... } }
```

El método `apiResponse()` en `app/Http/Controllers/Controller.php` genera este formato.
`destroy()` usa `response()->json(null, 204)` directamente (sin body).

## Reglas de delete

| Tipo | Módulos | Comportamiento al eliminar |
|------|---------|---------------------------|
| Hard delete | categorias, roles, inventario | Elimina definitivamente. Si tiene dependencias → 409 con mensaje claro |
| Soft delete | productos, almacenes, usuarios | Setea `activo = false` + `deleted_at`. Registro permanece en BD |

**Hard delete con dependencias — patrón obligatorio:**
1. Model tiene relación `hasMany` hacia la tabla dependiente
2. Service verifica con `$model->relacion()->withTrashed()->exists()` antes de borrar
3. Si hay dependencias lanza `\RuntimeException('Mensaje claro en español')`
4. Controller captura con `try/catch` y retorna 409

**Soft delete — patrón obligatorio:**
```php
// En el Service::eliminar()
$model->activo = false;
$model->save();
return $this->repository->delete($model); // setea deleted_at
```

## Convención de rutas con apiResource

Cuando `Str::singular()` genera un nombre incorrecto, forzar el parámetro con `->parameters()`:

```php
// routes/api.php
Route::apiResource('almacenes', AlmacenController::class)
    ->parameters(['almacenes' => 'almacen']); // evita {almacene}

Route::apiResource('roles', RolController::class)
    ->parameters(['roles' => 'rol']);          // evita {role}
```

El nombre del parámetro de ruta DEBE coincidir con la variable del controller (`$almacen`, `$rol`).

## Base de datos

```
categorias          productos                 almacenes
──────────          ─────────                 ─────────
id                  id                        id
nombre              cod_producto (unique)      nombre
descripcion         nombre                    descripcion (nullable)
timestamps          categoria_id (FK→cat)     direccion
                    marca (nullable)          responsable
                    unidad_medida             telefono (nullable)
                    contenido (nullable)      activo boolean
                    precio_compra             deleted_at
                    precio_minorista          timestamps
                    precio_mayorista
                    stock_minimo              inventario
                    activo boolean            ─────────
                    deleted_at                id
                    timestamps                producto_id (FK→productos)
                                              almacen_id (FK→almacenes)
roles                                         cantidad
─────                                         cantidad_reservada
id                  usuarios                  cantidad_minima
nombre              ────────                  ultima_actualizacion
descripcion         id                        timestamps
timestamps          rol_id (FK→roles)
                    username (unique)         ventas
                    email (unique)            ──────
                    password_hash             id
                    must_change_password      usuario_id (FK→usuarios)
                    activo boolean            almacen_id (FK→almacenes)
                    ultimo_acceso (nullable)  fecha (date)
                    deleted_at                cliente
                    timestamps                total decimal(10,2)
                                              numero_comprobante (unique)
                                              tipo_pago (enum)
                                              estado (enum)
                                              activo boolean
                                              deleted_at
                                              timestamps
```

**Enums ventas:**
- `tipo_pago`: efectivo | tarjeta | transferencia | otro
- `estado`: pendiente | completada | cancelada

**Relaciones Eloquent:**
- `Categoria` hasMany `Producto` · `Producto` belongsTo `Categoria`
- `Rol` hasMany `Usuario` · `Usuario` belongsTo `Rol`
- `Almacen` ↔ `Producto` via `Inventario`
- `Venta` belongsTo `Usuario` · `Venta` belongsTo `Almacen`

## Endpoints implementados

```
# Categorías (hard delete, 409 si tiene productos)
GET    /api/v1/categorias              ?search= &per_page=
POST   /api/v1/categorias
GET    /api/v1/categorias/{categoria}
PUT    /api/v1/categorias/{categoria}
PATCH  /api/v1/categorias/{categoria}
DELETE /api/v1/categorias/{categoria}  → 204 | 409

# Productos (soft delete)
GET    /api/v1/productos               ?search= &categoria_id= &activo= &con_eliminados= &per_page=
POST   /api/v1/productos
GET    /api/v1/productos/{producto}
PUT    /api/v1/productos/{producto}
PATCH  /api/v1/productos/{producto}
DELETE /api/v1/productos/{producto}    → 204

# Almacenes (soft delete)
GET    /api/v1/almacenes               ?search= &activo= &con_eliminados= &per_page=
POST   /api/v1/almacenes
GET    /api/v1/almacenes/{almacen}
PUT    /api/v1/almacenes/{almacen}
PATCH  /api/v1/almacenes/{almacen}
DELETE /api/v1/almacenes/{almacen}     → 204

# Inventario (hard delete)
GET    /api/v1/inventario              ?producto_id= &almacen_id= &bajo_minimo= &per_page=
POST   /api/v1/inventario
GET    /api/v1/inventario/{inventario}
PUT    /api/v1/inventario/{inventario}
PATCH  /api/v1/inventario/{inventario}
DELETE /api/v1/inventario/{inventario} → 204

# Roles (hard delete, 409 si tiene usuarios)
GET    /api/v1/roles                   ?search=
POST   /api/v1/roles
GET    /api/v1/roles/{rol}
PUT    /api/v1/roles/{rol}
PATCH  /api/v1/roles/{rol}
DELETE /api/v1/roles/{rol}             → 204 | 409

# Usuarios (soft delete)
GET    /api/v1/usuarios                ?search= &rol_id= &activo= &con_eliminados=
POST   /api/v1/usuarios
GET    /api/v1/usuarios/{usuario}
PUT    /api/v1/usuarios/{usuario}
PATCH  /api/v1/usuarios/{usuario}
DELETE /api/v1/usuarios/{usuario}      → 204

# Ventas (soft delete)
GET    /api/v1/ventas                  ?search= &usuario_id= &almacen_id= &estado= &tipo_pago= &fecha_desde= &fecha_hasta= &con_eliminados=
POST   /api/v1/ventas
GET    /api/v1/ventas/{venta}
PUT    /api/v1/ventas/{venta}
PATCH  /api/v1/ventas/{venta}
DELETE /api/v1/ventas/{venta}          → 204
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
AlmacenRepositoryInterface   → AlmacenRepository
AlmacenServiceInterface      → AlmacenService
InventarioRepositoryInterface → InventarioRepository
InventarioServiceInterface   → InventarioService
RolRepositoryInterface       → RolRepository
RolServiceInterface          → RolService
UsuarioRepositoryInterface   → UsuarioRepository
UsuarioServiceInterface      → UsuarioService
VentaRepositoryInterface     → VentaRepository
VentaServiceInterface        → VentaService
```

Provider registrado en `bootstrap/app.php` via `withProviders()`.

## Orden de implementación

1. ✅ Categorías CRUD — hard delete, 409 si tiene productos
2. ✅ Productos CRUD — soft delete, filtros, relación con Categoria
3. ✅ Swagger UI — documentación interactiva en /docs
4. ✅ Almacenes CRUD — soft delete
5. ✅ Inventario CRUD — hard delete, pivot almacen/producto
6. ✅ Roles CRUD — hard delete, 409 si tiene usuarios
7. ✅ Usuarios CRUD — soft delete, password hasheado, relación con Rol
8. ✅ Ventas CRUD — soft delete, filtros por fecha/estado/tipo_pago, embeds usuario y almacen
9. ✅ Movimientos — auditoría de entradas/salidas, actualiza inventario en transacción
10. ⬜ Detalle ventas (detalle_ventas con FK venta_id)
11. ⬜ Auth JWT (login, register, logout, me)
12. ⬜ Proteger rutas con middleware `auth:api`
