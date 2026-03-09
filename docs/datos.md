Excelente decisión. Antes de implementar, analicemos bien porque la
  estrategia de tokens tiene impacto directo en cómo Zustand y TanStack
  Query deben configurarse.

  ---
  Análisis y evaluación

  ¿Por qué JWT + Refresh Token?

  Un JWT solo tiene dos problemas fundamentales:

  Problema 1 — TTL largo:
    Token válido 24h → si alguien lo roba, tiene 24h de acceso libre

  Problema 2 — TTL corto:
    Token válido 15min → usuario tiene que hacer login cada 15min (terrible
  UX)

  Solución → Access Token corto + Refresh Token largo

  ---
  Estrategia recomendada para tu stack

  ACCESS TOKEN (JWT)              REFRESH TOKEN
  ──────────────────              ─────────────
  TTL: 60 minutos                 TTL: 30 días
  Guardado: Zustand (memoria)     Guardado: httpOnly cookie
  Viaja en: Authorization header  Viaja en: cookie automática
  Vulnerable a: XSS               Protegido de: XSS (httpOnly)

  ¿Por qué Zustand en memoria y NO localStorage?

  localStorage  → XSS puede leer el token con document.cookie /
  localStorage.getItem()
  Zustand       → XSS no puede acceder a memoria de React

  ¿Por qué el refresh token en httpOnly cookie y NO en Zustand?

  httpOnly cookie → el navegador la envía automáticamente
                 → JavaScript NO puede leerla (document.cookie no la ve)
                 → sobrevive al refresh de página
                 → TanStack Query llama /auth/refresh y el navegador
                   adjunta la cookie solo

  ---
  Flujo completo con Zustand + TanStack Query

  LOGIN
    │
    ├── POST /auth/login → { access_token }  +  Set-Cookie: refresh_token
  (httpOnly)
    │
    ├── Zustand.setToken(access_token)
    └── TanStack Query usa el token en cada request

  REQUEST NORMAL
    │
    └── TanStack Query → Authorization: Bearer {access_token}

  TOKEN EXPIRADO (401)
    │
    ├── Axios interceptor detecta 401
    ├── POST /auth/refresh  (cookie va automática, sin código extra)
    ├── Servidor valida refresh token, rota el refresh token (genera uno
  nuevo)
    ├── Devuelve nuevo { access_token } + nueva cookie refresh_token
    ├── Zustand.setToken(nuevo_access_token)
    └── Reintenta el request original

  LOGOUT
    │
    ├── POST /auth/logout
    ├── Servidor blacklistea el access token
    ├── Servidor borra la cookie (Set-Cookie: refresh_token=; Max-Age=0)
    └── Zustand.clearToken()

  ---
  ¿Refresh token en DB o solo en cookie?

  ┌─────────────────────────────┬────────────┬──────────────────────────┐
  │                             │   Solo     │     DB + cookie ✅       │
  │                             │   cookie   │       recomendado        │
  ├─────────────────────────────┼────────────┼──────────────────────────┤
  │ Revocar sesión específica   │ ❌         │ ✅                       │
  ├─────────────────────────────┼────────────┼──────────────────────────┤
  │ Ver sesiones activas        │ ❌         │ ✅                       │
  ├─────────────────────────────┼────────────┼──────────────────────────┤
  │ Rotación de tokens          │ manual     │ automática               │
  ├─────────────────────────────┼────────────┼──────────────────────────┤
  │ Logout desde otro           │ ❌         │ ✅                       │
  │ dispositivo                 │            │                          │
  ├─────────────────────────────┼────────────┼──────────────────────────┤
  │ Complejidad                 │ baja       │ media                    │
  └─────────────────────────────┴────────────┴──────────────────────────┘

  Recomendación: DB + cookie. Para una licorería con múltiples vendedores y
  dispositivos, poder revocar sesiones individuales es importante.

  ---
  Tabla refresh_tokens en BD

  refresh_tokens
  ──────────────
  id
  usuario_id   FK → usuarios
  token        string(64) unique — guardado como SHA-256 hash
  expires_at   timestamp
  created_at   timestamp

  ---
  Endpoints a implementar

  POST  /api/v1/auth/login       → access_token + cookie refresh_token
  POST  /api/v1/auth/logout      → blacklist + borra cookie
  POST  /api/v1/auth/refresh     → nuevo access_token + rota cookie
  GET   /api/v1/auth/me          → datos del usuario autenticado

  ---
  Protección de rutas existentes

  // Todas las rutas actuales quedan protegidas con middleware JWT
  Route::middleware('auth:api')->group(function () {
      Route::apiResource('categorias', ...);
      Route::apiResource('productos', ...);
      // ... todas las demás
  });

  ---
  Archivos a crear/modificar

  Nuevos (8 archivos):
  database/migrations/..._create_refresh_tokens_table.php
  app/Models/RefreshToken.php
  app/Http/Controllers/Api/AuthController.php
  app/Http/Requests/LoginRequest.php
  app/Services/Contracts/AuthServiceInterface.php
  app/Services/AuthService.php
  app/Http/Middleware/JwtMiddleware.php

  Modificados (4 archivos):
  app/Models/Usuario.php          → implementar JWTSubject
  config/auth.php                 → guard 'api' con driver jwt
  routes/api.php                  → rutas auth + middleware en todas
  app/Providers/RepositoryServiceProvider.php → binding AuthService

  Más instalación del paquete:
  composer require tymon/jwt-auth
  php artisan vendor:publish
  --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
  php artisan jwt:secret

  ---
  Variables de entorno a agregar

  JWT_SECRET=           # generado por php artisan jwt:secret
  JWT_TTL=60            # minutos — access token
  JWT_REFRESH_TTL=43200 # minutos = 30 días — NO se usa para refresh (lo
  maneja DB)

  ---
  ¿Todo claro con el plan? ¿Arrancamos la implementación?

✻ Churned for 1m 1s

> si esta perfecto para el endpoint tambien es necesario validar por favro
que el usuario tenga el campo activo en true para q se peuda loguear, y
tambien necesito analizar dos partes una en que el usuario con admin puede
aceder a todo los endpoint y por otra parte el suuario con rol vendedor que
 es como estoy trabajando mis roles este vendedor solo pueda hacer la parte
 operativa que es de registrara las ventas, por favor con estas
consideeraciones analisesmos y por favor evalualo y con un analisis critico
 evalua si estoy en lo correcto o no?

● Análisis crítico de tu propuesta

  Lo que propones ✅ correcto en esencia, pero con un problema de lógica

  Tu razonamiento "vendedor = solo ventas" tiene un error operativo que te
  va a bloquear en el frontend:

  ---
  El problema:

  Vendedor quiere crear una venta → necesita elegir un producto
                                            ↓
                ¿De dónde saca la lista de productos? → GET /productos
                ¿Cómo sabe si hay stock?              → GET /inventario
                ¿Qué almacén está usando?              → GET /almacenes

  Si bloqueas esos endpoints al vendedor... no puede operar.

  Un vendedor en el mundo real necesita leer el catálogo para hacer su
  trabajo. Lo que NO debe poder hacer es administrarlo.

  ---
  La distinción correcta no es por módulo, es por verbo HTTP

  MÓDULO          VENDEDOR            ADMIN
  ──────────────────────────────────────────────────────
  categorias      GET (leer)          GET + POST + PUT + DELETE
  productos       GET (leer)          GET + POST + PUT + DELETE
  almacenes       GET (leer)          GET + POST + PUT + DELETE
  inventario      GET (leer)          GET + POST + PUT + DELETE
  roles           ❌ nada             TODO
  usuarios        ❌ nada             TODO
  movimientos     GET (leer)          GET + POST (manual)
  ventas          GET + POST          GET + POST + PUT + PATCH + DELETE
  detalle_ventas  GET + POST          GET + POST

  La regla es:
  - Vendedor = leer catálogo + operar ventas
  - Admin = todo, incluyendo gestión de catálogo, usuarios, configuración

  ---
  Segundo punto crítico: ¿Quién puede cambiar el estado de una venta?

  Tu flujo actual: vendedor crea venta → agrega detalles → cambia estado a
  completada.

  La pregunta es: ¿puede el vendedor cancelar una venta?

  Opción A — Vendedor puede completar Y cancelar su propia venta
    PRO: flujo operativo completo sin depender del admin
    CON: vendedor podría cancelar ventas de otro vendedor

  Opción B — Solo admin puede cancelar ventas
    PRO: control total sobre cancelaciones (impacto financiero)
    CON: vendedor queda bloqueado si se equivoca

  Opción C ✅ recomendada — Vendedor puede completar sus ventas,
                            solo admin puede cancelar
    Lógica: completar = cierre normal | cancelar = acción administrativa

  ---
  Tercer punto crítico: movimientos manuales

  Tienes POST /api/v1/movimientos para registrar entradas de stock
  manualmente. Esto es una operación administrativa (reposición de
  inventario), no operativa de ventas.

  ¿Puede un vendedor hacer una entrada manual de stock? → NO
  Solo el admin o un rol futuro "almacenero" debería poder hacerlo.

  ---
  Propuesta final de permisos

  ENDPOINT                        VENDEDOR     ADMIN
  ────────────────────────────────────────────────────
  GET    /categorias              ✅           ✅
  POST   /categorias              ❌           ✅
  PUT    /categorias/{id}         ❌           ✅
  DELETE /categorias/{id}         ❌           ✅

  GET    /productos                ✅           ✅
  POST   /productos                ❌           ✅
  PUT    /productos/{id}           ❌           ✅
  DELETE /productos/{id}           ❌           ✅

  GET    /almacenes                ✅           ✅
  POST   /almacenes                ❌           ✅
  PUT    /almacenes/{id}           ❌           ✅
  DELETE /almacenes/{id}           ❌           ✅

  GET    /inventario               ✅           ✅
  POST   /inventario               ❌           ✅
  PUT    /inventario/{id}          ❌           ✅
  DELETE /inventario/{id}          ❌           ✅

  GET    /movimientos              ✅           ✅
  POST   /movimientos              ❌           ✅ (manual)

  GET    /ventas                   ✅           ✅
  POST   /ventas                   ✅           ✅
  GET    /ventas/{id}              ✅           ✅
  PATCH  /ventas/{id}              ✅ *         ✅
    * vendedor solo puede → completada
    * admin puede → completada | cancelada
  DELETE /ventas/{id}              ❌           ✅

  GET    /ventas/{id}/detalles     ✅           ✅
  POST   /ventas/{id}/detalles     ✅           ✅
  GET    /ventas/{id}/detalles/{d} ✅           ✅

  GET    /roles                    ❌           ✅
  POST   /roles                    ❌           ✅
  ...todos roles                   ❌           ✅

  GET    /usuarios                 ❌           ✅
  POST   /usuarios                 ❌           ✅
  ...todos usuarios                ❌           ✅

  GET    /auth/me                  ✅           ✅
  POST   /auth/logout              ✅           ✅
  POST   /auth/refresh             ✅           ✅
