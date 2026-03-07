# Backend Laravel — Guía de Arquitectura
## Proyecto: Categorías, Productos y Almacén

---

## Stack
- **Laravel 12** — Framework PHP
- **Eloquent** — ORM
- **JWT** (tymon/jwt-auth) — Autenticación
- **MySQL** — Base de datos
- **Docker** — Base de datos local

---

## Principios que seguiremos

### SOLID aplicado a Laravel

| Principio | Qué significa | Cómo se aplica |
|-----------|---------------|----------------|
| **S** — Single Responsibility | Cada clase hace una sola cosa | Controller solo recibe/responde, Service contiene lógica |
| **O** — Open/Closed | Abierto para extender, cerrado para modificar | Interfaces en Repository |
| **D** — Dependency Inversion | Depender de abstracciones, no de implementaciones | Inyectar interfaces, no clases concretas |

---

## Arquitectura en Capas

```
HTTP Request
     ↓
┌─────────────────────────────────────┐
│  ROUTES (routes/api.php)            │  → Define endpoints
└─────────────────────────────────────┘
     ↓
┌─────────────────────────────────────┐
│  CONTROLLER (Http/Controllers/Api)  │  → Recibe request, devuelve response
└─────────────────────────────────────┘
     ↓
┌─────────────────────────────────────┐
│  SERVICE (Services/)                │  → Lógica de negocio
└─────────────────────────────────────┘
     ↓
┌─────────────────────────────────────┐
│  REPOSITORY (Repositories/)         │  → Acceso a datos (abstracción)
└─────────────────────────────────────┘
     ↓
┌─────────────────────────────────────┐
│  MODEL (Models/)                    │  → Eloquent, mapea tabla
└─────────────────────────────────────┘
     ↓
   MySQL
```

### ¿Por qué esta separación?

- **Controller** — no sabe nada de base de datos, solo habla HTTP
- **Service** — no sabe nada de HTTP, solo aplica reglas de negocio
- **Repository** — no sabe nada de negocio, solo consulta datos
- **Model** — no sabe nada de lógica, solo representa una tabla

---

## Estructura de carpetas

```
app/
├── Http/
│   └── Controllers/
│       ├── AuthController.php
│       ├── CategoriaController.php
│       ├── ProductoController.php
│       ├── AlmacenController.php
│       └── dto/
│           ├── request/                ← Validaciones (Form Requests)
│           │   ├── StoreProductoRequest.php
│           │   └── StoreCategoriaRequest.php
│           └── response/               ← Formateo de respuestas JSON
│               ├── ProductoResource.php
│               └── CategoriaResource.php
│
├── Services/                   ← Lógica de negocio
│   ├── CategoriaService.php
│   ├── ProductoService.php
│   └── AlmacenService.php
│
├── Repositories/               ← Acceso a datos
│   ├── Contracts/              ← Interfaces
│   │   ├── CategoriaRepositoryInterface.php
│   │   └── ProductoRepositoryInterface.php
│   ├── CategoriaRepository.php
│   └── ProductoRepository.php
│
├── Models/
│   ├── Categoria.php
│   ├── Producto.php
│   └── Almacen.php
│
└── Providers/
    └── RepositoryServiceProvider.php  ← Bind interfaces → implementaciones
```

---

## Base de datos

### Tablas y relaciones

```
categorias
├── id
├── nombre
├── descripcion
└── timestamps

productos
├── id
├── nombre
├── descripcion
├── precio
├── stock
├── categoria_id (FK → categorias.id)
└── timestamps

almacenes
├── id
├── nombre
├── ubicacion
└── timestamps

almacen_producto (pivot)
├── id
├── almacen_id (FK → almacenes.id)
├── producto_id (FK → productos.id)
├── cantidad
└── timestamps
```

### Relaciones Eloquent

```
Categoria → hasMany → Producto
Producto  → belongsTo → Categoria
Producto  → belongsToMany → Almacen (pivot: almacen_producto)
Almacen   → belongsToMany → Producto (pivot: almacen_producto)
```

---

## JWT — Autenticación

### Flujo

```
POST /api/v1/auth/login
     ↓ email + password
AuthController valida credenciales
     ↓
JWT genera token
     ↓
{ token: "eyJ..." }
     ↓
Cliente guarda el token
     ↓
GET /api/v1/productos
Authorization: Bearer eyJ...
     ↓
Middleware verifica token
     ↓
Controller responde
```

### Rutas protegidas vs públicas

```php
// Públicas
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Protegidas con JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('almacenes', AlmacenController::class);
});
```

---

## Form Requests — Validaciones

En vez de validar en el controller, cada endpoint tiene su propia clase de validación:

```php
// Http/Requests/StoreProductoRequest.php
public function rules(): array
{
    return [
        'nombre'       => 'required|string|max:255',
        'precio'       => 'required|numeric|min:0',
        'stock'        => 'required|integer|min:0',
        'categoria_id' => 'required|exists:categorias,id',
    ];
}
```

---

## API Resources — Respuestas consistentes

Formatean la respuesta JSON, evitan exponer columnas innecesarias:

```php
// Http/Resources/ProductoResource.php
public function toArray($request): array
{
    return [
        'id'        => $this->id,
        'nombre'    => $this->nombre,
        'precio'    => $this->precio,
        'stock'     => $this->stock,
        'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
    ];
}
```

---

## Repository Pattern

### Interface (contrato)
```php
// Repositories/Contracts/ProductoRepositoryInterface.php
interface ProductoRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Producto;
    public function create(array $data): Producto;
    public function update(int $id, array $data): Producto;
    public function delete(int $id): bool;
}
```

### Implementación
```php
// Repositories/ProductoRepository.php
class ProductoRepository implements ProductoRepositoryInterface
{
    public function all(): Collection
    {
        return Producto::with('categoria')->get();
    }
    // ...
}
```

### Bind en ServiceProvider
```php
// Providers/RepositoryServiceProvider.php
$this->app->bind(
    ProductoRepositoryInterface::class,
    ProductoRepository::class
);
```

---

## Endpoints planeados

```
POST   /api/v1/auth/login
POST   /api/v1/auth/register
POST   /api/v1/auth/logout        [auth]
GET    /api/v1/auth/me            [auth]

GET    /api/v1/categorias         [auth]
POST   /api/v1/categorias         [auth]
GET    /api/v1/categorias/{id}    [auth]
PUT    /api/v1/categorias/{id}    [auth]
DELETE /api/v1/categorias/{id}    [auth]

GET    /api/v1/productos          [auth]
POST   /api/v1/productos          [auth]
GET    /api/v1/productos/{id}     [auth]
PUT    /api/v1/productos/{id}     [auth]
DELETE /api/v1/productos/{id}     [auth]

GET    /api/v1/almacenes          [auth]
POST   /api/v1/almacenes          [auth]
GET    /api/v1/almacenes/{id}     [auth]
PUT    /api/v1/almacenes/{id}     [auth]
DELETE /api/v1/almacenes/{id}     [auth]
POST   /api/v1/almacenes/{id}/productos  [auth]  ← asignar producto a almacén
```

---

## Configuración para producción

**Solo cambias el `.env`**, el código no toca:**

```env
# ── App ──────────────────────────────
APP_NAME="Mi App"
APP_ENV=production        # local → production
APP_DEBUG=false           # true  → false  (CRÍTICO)
APP_URL=https://tudominio.com

# ── Base de datos ─────────────────────
DB_CONNECTION=mysql
DB_HOST=tu-servidor-mysql
DB_PORT=3302
DB_DATABASE=nombre_bd
DB_USERNAME=usuario
DB_PASSWORD=password_seguro

# ── JWT ───────────────────────────────
JWT_SECRET=clave_generada_con_artisan
JWT_TTL=60                # minutos que dura el token

# ── Cache y Sesiones ──────────────────
CACHE_STORE=redis         # database → redis en prod
SESSION_DRIVER=redis

# ── Queue ─────────────────────────────
QUEUE_CONNECTION=redis    # database → redis en prod

# ── Mail ──────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.tuproveedor.com
MAIL_PORT=587
MAIL_USERNAME=correo@tudominio.com
MAIL_PASSWORD=password
```

### Comandos para producción

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache      # cachea configuración
php artisan route:cache       # cachea rutas
php artisan view:cache        # cachea vistas
php artisan migrate --force   # migra en producción
```

---

## Respuesta estándar de la API

Todas las respuestas seguirán este formato:

```json
// Éxito
{
  "success": true,
  "data": { ... },
  "message": "Producto creado correctamente"
}

// Error
{
  "success": false,
  "message": "No autorizado",
  "errors": { ... }
}
```

---

## Orden de implementación

1. Instalar JWT (`tymon/jwt-auth`)
2. Modelos + Migraciones (Categoria, Producto, Almacen)
3. Seeders básicos
4. Repository Interfaces + Implementaciones
5. Services
6. Form Requests
7. API Resources
8. Controllers
9. Rutas
10. Pruebas con Postman/Thunder Client
