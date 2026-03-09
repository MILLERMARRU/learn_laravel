# Autenticación JWT — Documentación técnica

Sistema de autenticación implementado con `tymon/jwt-auth` usando **doble token**:
access token de corta vida + refresh token rotativo almacenado en cookie httpOnly.

---

## Índice

1. [Objetivo y decisiones de diseño](#1-objetivo-y-decisiones-de-diseño)
2. [Archivos involucrados](#2-archivos-involucrados)
3. [Flujo completo de autenticación](#3-flujo-completo-de-autenticación)
4. [Componentes en detalle](#4-componentes-en-detalle)
5. [Seguridad](#5-seguridad)
6. [Variables de entorno](#6-variables-de-entorno)

---

## 1. Objetivo y decisiones de diseño

### ¿Por qué JWT?
Laravel usa sesiones con cookies por defecto, pero una API REST debe ser **stateless** (sin estado en el servidor). JWT permite enviar la identidad del usuario dentro del propio token, sin necesidad de consultar la BD en cada request.

### Estrategia de doble token

```
┌─────────────────────────────────────────────────────────────┐
│                   ESTRATEGIA DE TOKENS                      │
├──────────────────────┬──────────────────────────────────────┤
│   ACCESS TOKEN (JWT) │   REFRESH TOKEN                      │
├──────────────────────┼──────────────────────────────────────┤
│ Duración: 60 min     │ Duración: 30 dias                   │
│ Viaja en: Header     │ Viaja en: Cookie httpOnly            │
│           Authorization: Bearer <token>                     │
│ Almacén:  Memoria    │ Almacén: BD (hasheado SHA-256)       │
│           (Zustand)  │          + Cookie navegador          │
│ JS lee:   Sí         │ JS lee:  NO (httpOnly)               │
└──────────────────────┴──────────────────────────────────────┘
```

**¿Por qué dos tokens?**
- El **access token** viaja en cada request — si lo roban dura máximo 60 min
- El **refresh token** solo viaja al renovar el access token — cookie httpOnly, JS no puede robarlo con XSS
- Con **rotación**: cada refresh genera un nuevo par de tokens e invalida el anterior

---

## 2. Archivos involucrados

```
app/
├── Models/
│   ├── Usuario.php               ← Implementa JWTSubject + Authenticatable
│   └── RefreshToken.php          ← Modelo del refresh token en BD
│
├── Services/
│   ├── Contracts/
│   │   └── AuthServiceInterface.php   ← Interfaz del servicio
│   └── AuthService.php                ← Lógica: login, refresh, logout, me
│
├── Http/
│   ├── Controllers/Api/
│   │   └── AuthController.php    ← HTTP: cookies, respuestas JSON
│   ├── Requests/
│   │   └── LoginRequest.php      ← Validación de credenciales
│   ├── Resources/
│   │   └── UsuarioResource.php   ← Formateo seguro (sin password_hash)
│   └── Middleware/
│       └── RoleMiddleware.php    ← Control de acceso por rol
│
├── Providers/
│   └── RepositoryServiceProvider.php  ← Bind AuthServiceInterface
│
config/
├── auth.php                      ← Guard 'api' con driver 'jwt'
└── jwt.php                       ← Configuración tymon/jwt-auth
│
bootstrap/
└── app.php                       ← Alias 'role', excepciones JWT, encryptCookies
│
routes/
└── api.php                       ← Rutas públicas vs protegidas
│
database/migrations/
├── ..._create_usuarios_table.php
└── ..._create_refresh_tokens_table.php
```

---

## 3. Flujo completo de autenticación

### 3.1 Login

```
Frontend                     AuthController              AuthService              BD
   │                               │                          │                   │
   │── POST /api/v1/auth/login ───►│                          │                   │
   │   { email, password }         │                          │                   │
   │                               │── login($credentials) ──►│                  │
   │                               │                          │── SELECT usuario  │
   │                               │                          │   WHERE email=?  ►│
   │                               │                          │◄── Usuario model ─│
   │                               │                          │                   │
   │                               │                          │ Hash::check(pass, password_hash)
   │                               │                          │ verifica activo=true
   │                               │                          │                   │
   │                               │                          │── JWTAuth::fromUser($usuario)
   │                               │                          │   genera JWT firmado con JWT_SECRET
   │                               │                          │                   │
   │                               │                          │── crearRefreshToken()
   │                               │                          │   random_bytes(32) → hex string
   │                               │                          │   SHA-256(token) → INSERT refresh_tokens ►│
   │                               │                          │   retorna token en crudo               │
   │                               │                          │                   │
   │                               │◄── { access_token,       │                   │
   │                               │     refresh_token, ... } │                   │
   │                               │                          │                   │
   │                               │ buildRefreshCookie(refresh_token)
   │                               │ Cookie: httpOnly, Strict, path=/api/v1/auth
   │                               │                          │                   │
   │◄── 200 { access_token, ... } ─│                          │                   │
   │    Set-Cookie: refresh_token  │                          │                   │
   │                               │                          │                   │
   │ Zustand guarda access_token   │                          │                   │
   │ en memoria (no localStorage)  │                          │                   │
```

### 3.2 Request autenticado

```
Frontend                     Middleware (auth:api)         JWTAuth              BD
   │                               │                          │                   │
   │── GET /api/v1/ventas ────────►│                          │                   │
   │   Authorization: Bearer <JWT> │                          │                   │
   │                               │── JWTAuth::parseToken() ►│                  │
   │                               │                          │ Verifica firma con JWT_SECRET
   │                               │                          │ Verifica exp (no vencido)
   │                               │                          │ Verifica blacklist
   │                               │◄── Usuario autenticado ──│                  │
   │                               │                          │                   │
   │                               │── $next($request) ──────►VentaController     │
   │                               │                          │                   │
   │◄── 200 { ventas... } ─────────│                          │                   │
```

### 3.3 Renovación de token (refresh)

```
Frontend (TanStack Query)    AuthController              AuthService              BD
   │                               │                          │                   │
   │ access_token vence (401)      │                          │                   │
   │── POST /api/v1/auth/refresh ─►│                          │                   │
   │   Cookie: refresh_token=xxx   │ (cookie llega automáticamente)               │
   │   (navegador la envía solo)   │                          │                   │
   │                               │── refresh($cookie) ─────►│                  │
   │                               │                          │ SHA-256(token)     │
   │                               │                          │── SELECT refresh_tokens
   │                               │                          │   WHERE token=hash►│
   │                               │                          │◄── registro ──────│
   │                               │                          │                   │
   │                               │                          │ estaVencido() ?    │
   │                               │                          │ usuario activo ?   │
   │                               │                          │                   │
   │                               │                          │── DELETE old token►│ (rotación)
   │                               │                          │── JWTAuth::fromUser() → nuevo JWT
   │                               │                          │── crearRefreshToken() → nuevo token
   │                               │                          │── INSERT nuevo refresh_tokens ►│
   │                               │                          │                   │
   │◄── 200 { access_token } ──────│                          │                   │
   │    Set-Cookie: refresh_token  │ (nueva cookie)           │                   │
   │    (nuevo token)              │                          │                   │
   │                               │                          │                   │
   │ Zustand actualiza access_token│                          │                   │
   │ TanStack reintenta request    │                          │                   │
   │ original automáticamente      │                          │                   │
```

### 3.4 Control de acceso por rol

```
Request                  auth:api                 RoleMiddleware           Controller
   │                        │                          │                       │
   │── PUT /api/v1/roles ──►│                          │                       │
   │   Authorization: Bearer│                          │                       │
   │                        │ JWT válido → usuario OK  │                       │
   │                        │──────────────────────────►│                      │
   │                        │                          │ usuario->rol->nombre   │
   │                        │                          │ in_array('Administrador', $roles) ?
   │                        │                          │                       │
   │                        │                          │── sí ────────────────►│
   │                        │                          │   $next($request)     │
   │                        │                          │                       │
   │                        │                          │── no → 403 Forbidden  │
```

### 3.5 Logout

```
Frontend                     AuthController              AuthService              BD
   │                               │                          │                   │
   │── POST /api/v1/auth/logout ──►│                          │                   │
   │   Authorization: Bearer <JWT> │                          │                   │
   │   Cookie: refresh_token=xxx   │                          │                   │
   │                               │── logout($refreshToken) ►│                  │
   │                               │                          │── JWTAuth::invalidate()
   │                               │                          │   agrega JWT a blacklist
   │                               │                          │── DELETE refresh_tokens
   │                               │                          │   WHERE token=SHA256 ►│
   │                               │ cookie()->forget()       │                   │
   │◄── 200 { message: OK } ───────│ Set-Cookie: expires=past │                   │
   │    cookie eliminada           │                          │                   │
   │ Zustand limpia access_token   │                          │                   │
```

---

## 4. Componentes en detalle

### 4.1 `app/Models/Usuario.php` — JWTSubject

Para que `tymon/jwt-auth` pueda generar tokens a partir del model, `Usuario` implementa dos interfaces:

```php
class Usuario extends Model implements Authenticatable, JWTSubject
```

| Método | Qué hace |
|--------|----------|
| `getJWTIdentifier()` | Retorna el ID del usuario — se incluye como `sub` en el payload del JWT |
| `getJWTCustomClaims()` | Agrega claims extras al JWT — aquí incluye `rol` para que el frontend lo lea sin consultar la BD |
| `getAuthPassword()` | Le dice a Laravel que el campo de contraseña es `password_hash` (no `password`) |

**Payload del JWT resultante:**
```json
{
  "sub": 3,
  "iat": 1741392000,
  "exp": 1741395600,
  "rol": "Vendedor"
}
```

### 4.2 `app/Models/RefreshToken.php` — Tabla de tokens

Almacena los refresh tokens **hasheados** en BD. Nunca el token en crudo.

```
refresh_tokens
──────────────
id
usuario_id    FK → usuarios (cascadeOnDelete)
token         string(64) unique  ← SHA-256 del token real
expires_at    timestamp
created_at    timestamp          ← sin updated_at (inmutable)
```

Método `estaVencido()`:
```php
public function estaVencido(): bool
{
    return $this->expires_at->isPast();
}
```

### 4.3 `app/Services/AuthService.php` — Lógica de negocio

Contiene las 4 operaciones de autenticación. No toca HTTP — solo lógica pura.

```
AuthServiceInterface (contrato)
    │
    └── AuthService (implementación)
            ├── login(array $credentials): array
            ├── refresh(string $refreshToken): array
            ├── logout(string $refreshToken): void
            ├── me(): Usuario
            └── crearRefreshToken(Usuario): string  [privado]
```

**Generación del refresh token (crearRefreshToken):**
```
random_bytes(32)           → 32 bytes aleatorios criptográficamente seguros
bin2hex(...)               → string de 64 caracteres hex (el token real)
hash('sha256', $tokenCrudo) → hash de 64 chars (lo que se guarda en BD)
```

### 4.4 `app/Http/Controllers/Api/AuthController.php` — Capa HTTP

Responsabilidad exclusiva: manejar la parte HTTP (cookies, headers, respuestas JSON). La lógica está en `AuthService`.

**buildRefreshCookie:**
```php
cookie(
    name:     'refresh_token',
    value:    $token,              // token en crudo
    minutes:  env('REFRESH_TOKEN_TTL', 1440),
    path:     '/api/v1/auth',      // solo se envía a /auth/*
    secure:   app()->isProduction(), // HTTPS en producción
    httpOnly: true,                // JS no puede leerla
    sameSite: 'Strict',            // no se envía en requests cross-site
);
```

### 4.5 `config/auth.php` — Guard JWT

```php
'guards' => [
    'api' => [
        'driver'   => 'jwt',       // usa tymon/jwt-auth
        'provider' => 'usuarios',  // usa el provider de abajo
    ],
],

'providers' => [
    'usuarios' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Usuario::class,
    ],
],
```

Sin esta configuración, `auth('api')` no sabría qué modelo usar ni cómo validar el JWT.

### 4.6 `bootstrap/app.php` — Configuración global

Tres responsabilidades relacionadas con JWT:

```php
// 1. Alias del middleware de roles
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
]);

// 2. La cookie refresh_token NO debe ser encriptada por Laravel
//    (la lee AuthController en crudo, luego la hashea en AuthService)
$middleware->encryptCookies(except: ['refresh_token']);

// 3. Excepciones JWT → respuesta JSON 401 estándar
$exceptions->render(function (TokenExpiredException $e, Request $request) { ... });
$exceptions->render(function (TokenInvalidException $e, Request $request) { ... });
$exceptions->render(function (JWTException $e, Request $request) { ... });
```

### 4.7 `routes/api.php` — Separación de rutas

```
/api/v1/
├── auth/
│   ├── POST login      ← SIN middleware (pública)
│   ├── POST refresh    ← SIN middleware (pública)
│   └── [auth:api]
│       ├── POST logout
│       └── GET  me
│
└── [auth:api]           ← Todo lo demás requiere JWT válido
    ├── ...lectura catálogo (admin + vendedor)
    ├── ...ventas (admin + vendedor)
    └── [role:Administrador]
        └── ...escritura, usuarios, roles
```

---

## 5. Seguridad

### Protecciones implementadas

| Amenaza | Protección |
|---------|-----------|
| **XSS roba tokens** | Refresh token en cookie httpOnly — JS no puede acceder |
| **CSRF usa cookie** | SameSite=Strict — cookie no se envía en requests cross-site |
| **Token robado sigue válido** | Access token expira en 60 min. Logout lo agrega a blacklist |
| **Refresh token robado** | Rotación: al usar un token se destruye y genera uno nuevo |
| **Contraseña en BD** | bcrypt con 12 rounds (`BCRYPT_ROUNDS=12`) |
| **Refresh token en BD** | Guardado como SHA-256 — si hackean la BD no obtienen el token real |
| **Impersonación de usuarios** | `lock_subject = true` en `config/jwt.php` |
| **HTTP sniffing** | Cookie `secure=true` en producción — solo viaja por HTTPS |

### Ciclo de vida de los tokens

```
LOGIN
  │
  ├── access_token  ──── 60 min ──── expira → 401 → ir a refresh
  │                                                        │
  └── refresh_token ── 24 horas ─── expira → 401 → ir a login
                             │
                             └── al usarse → se destruye → se crea uno nuevo (rotación)
```

---

## 6. Variables de entorno

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `JWT_SECRET` | string aleatorio | Clave para firmar los tokens. Generar con `php artisan jwt:secret` |
| `JWT_TTL` | `60` | Duración del access token en **minutos** |
| `REFRESH_TOKEN_TTL` | `43200` | Duración del refresh token y su cookie en **minutos** (43200 = 30 dias) |

```bash
# Generar JWT_SECRET (solo ejecutar una vez por entorno)
php artisan jwt:secret
```

> ⚠️ Cambiar `JWT_SECRET` en producción invalida **todos** los tokens activos de todos los usuarios.
