# Prompt — Implementación Frontend Auth con Zustand + TanStack Query

Copia este prompt completo y entrégaselo a la IA para implementar el frontend.

---

## PROMPT

Necesito implementar la capa de autenticación en un frontend con **arquitectura Screaming** (feature-based), usando **Zustand** para estado global del access token y **TanStack Query** para las peticiones HTTP con renovación automática de tokens.

---

### Stack del frontend

- **Framework:** (indica aquí el tuyo — Next.js 14 App Router / React + Vite / etc.)
- **Estado global:** Zustand
- **Peticiones HTTP:** TanStack Query (React Query v5) + Axios
- **Lenguaje:** TypeScript

---

### Arquitectura Screaming (feature-based)

La estructura de carpetas debe seguir screaming architecture. Todo lo relacionado con autenticación va dentro de `src/features/auth/`:

```
src/
├── features/
│   └── auth/
│       ├── api/
│       │   └── authApi.ts          ← funciones Axios que llaman al backend
│       ├── hooks/
│       │   ├── useLogin.ts         ← useMutation de TanStack Query
│       │   ├── useLogout.ts        ← useMutation de TanStack Query
│       │   └── useMe.ts            ← useQuery de TanStack Query
│       ├── store/
│       │   └── authStore.ts        ← Zustand store del access token
│       ├── types/
│       │   └── auth.types.ts       ← interfaces TypeScript
│       └── index.ts                ← barrel export
│
├── lib/
│   ├── axios.ts                    ← instancia Axios con interceptors
│   └── queryClient.ts              ← instancia QueryClient de TanStack
```

---

### API del backend — Endpoints exactos

**Base URL:** `http://localhost:8000/api/v1`

**Formato de respuesta estándar de TODOS los endpoints:**
```json
// Éxito
{
  "success": true,
  "message": "Descripción",
  "data": { ... }
}

// Error
{
  "success": false,
  "message": "Descripción del error",
  "errors": { ... }
}
```

---

#### POST `/auth/login` — Pública (sin token)

**Request body:**
```json
{
  "email": "admin@licoreria.com",
  "password": "Admin@Licoreria2025!"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Sesión iniciada correctamente.",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "usuario": {
      "id": 1,
      "username": "admin",
      "email": "admin@licoreria.com",
      "must_change_password": false,
      "activo": true,
      "ultimo_acceso": "2026-03-08 10:00:00",
      "eliminado_en": null,
      "rol": {
        "id": 1,
        "nombre": "Administrador",
        "descripcion": "Acceso total al sistema."
      },
      "creado_en": "2026-03-08 00:00:00",
      "actualizado_en": "2026-03-08 00:00:00"
    }
  }
}
```

**Response 401 (credenciales incorrectas o cuenta inactiva):**
```json
{
  "success": false,
  "message": "Credenciales incorrectas."
}
```

**Comportamiento importante:**
- El backend setea automáticamente una **cookie httpOnly** llamada `refresh_token` en la respuesta
- El frontend NO debe intentar leer ni manipular esa cookie — el navegador la gestiona solo
- El `access_token` de `data` debe guardarse en **Zustand** (memoria, NO localStorage)
- `expires_in` está en **segundos** (3600 = 60 minutos)

---

#### POST `/auth/refresh` — Pública (sin token en header)

**Request:** Sin body. La cookie `refresh_token` se envía automáticamente por el navegador.

**Importante:** Axios debe tener `withCredentials: true` para que el navegador envíe la cookie.

**Response 200:**
```json
{
  "success": true,
  "message": "Token renovado correctamente.",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "usuario": { ... }
  }
}
```

**Response 401 (refresh token inválido o expirado):**
```json
{
  "success": false,
  "message": "Refresh token inválido."
}
```

**Comportamiento importante:**
- El backend también rota la cookie: elimina la anterior y envía una nueva
- Si retorna 401 → el usuario debe ir a login (sesión expirada)

---

#### POST `/auth/logout` — Protegida (requiere Bearer token)

**Request headers:**
```
Authorization: Bearer <access_token>
```
Sin body. La cookie `refresh_token` se envía automáticamente.

**Response 200:**
```json
{
  "success": true,
  "message": "Sesión cerrada correctamente."
}
```

**Comportamiento importante:**
- El backend elimina el refresh token de BD y borra la cookie
- El frontend debe limpiar el access token de Zustand
- Redirigir al login

---

#### GET `/auth/me` — Protegida (requiere Bearer token)

**Request headers:**
```
Authorization: Bearer <access_token>
```

**Response 200:**
```json
{
  "success": true,
  "message": "Usuario autenticado.",
  "data": {
    "id": 1,
    "username": "admin",
    "email": "admin@licoreria.com",
    "must_change_password": false,
    "activo": true,
    "ultimo_acceso": "2026-03-08 10:00:00",
    "eliminado_en": null,
    "rol": {
      "id": 1,
      "nombre": "Administrador",
      "descripcion": "Acceso total al sistema."
    },
    "creado_en": "2026-03-08 00:00:00",
    "actualizado_en": "2026-03-08 00:00:00"
  }
}
```

**Response 401 (token expirado o inválido):**
```json
{
  "success": false,
  "message": "El token ha expirado. Por favor renuévalo en /auth/refresh."
}
```

---

### Comportamiento requerido del interceptor Axios

El interceptor es el componente más crítico. Debe:

1. **Request interceptor:** Adjuntar el `access_token` de Zustand en el header `Authorization: Bearer <token>` en cada petición automáticamente.

2. **Response interceptor — manejo de 401:**
   - Detectar respuesta 401
   - Llamar automáticamente a `POST /auth/refresh` (con `withCredentials: true`)
   - Si el refresh es exitoso:
     - Guardar el nuevo `access_token` en Zustand
     - Reintentar la petición original con el nuevo token
   - Si el refresh falla (401):
     - Limpiar Zustand
     - Redirigir al login
   - Evitar bucles infinitos: si la petición que falló YA ERA `/auth/refresh`, no volver a intentar

---

### Zustand store requerido

El store debe manejar:

```typescript
interface AuthState {
  accessToken: string | null        // el JWT en memoria
  usuario: Usuario | null           // datos del usuario logueado
  isAuthenticated: boolean          // derivado de accessToken !== null

  // Acciones
  setAuth: (token: string, usuario: Usuario) => void   // tras login exitoso
  clearAuth: () => void                                  // tras logout o refresh fallido
  setAccessToken: (token: string) => void               // tras refresh exitoso
}
```

**Regla crítica:** El `accessToken` NUNCA debe persistirse en `localStorage` ni `sessionStorage`. Solo en memoria (Zustand sin `persist` middleware). Al recargar la página, se pierde — el interceptor intenta el refresh automáticamente.

---

### Flujo de refresco automático al recargar la página

Al montar la app (en el layout raíz o `_app.tsx`):

```
App monta
   │
   ├── Zustand: accessToken = null (memoria limpia)
   │
   ├── Llamar silenciosamente POST /auth/refresh
   │       │
   │       ├── 200 → guardar nuevo accessToken en Zustand → usuario sigue logueado
   │       │
   │       └── 401 → no hacer nada → mostrar pantalla de login
```

---

### Roles disponibles en el backend

```typescript
type Rol = 'Administrador' | 'Vendedor'
```

El rol viene dentro de `usuario.rol.nombre` en la respuesta del login y del `/me`. Úsalo para mostrar/ocultar elementos de la UI o proteger rutas.

---

### Credenciales de prueba (entorno local)

| Usuario | Email | Password | Rol |
|---------|-------|----------|-----|
| admin | admin@licoreria.com | Admin@Licoreria2025! | Administrador |
| miller | miller@licoreria.com | Password123! | Administrador |
| carlos | carlos@licoreria.com | Password123! | Vendedor |
| lucia | lucia@licoreria.com | Password123! | Vendedor |

---

### Resumen de lo que necesito implementado

1. `src/lib/axios.ts` — instancia Axios con `baseURL`, `withCredentials: true` e interceptors (adjuntar token + manejar 401 con refresh automático)
2. `src/lib/queryClient.ts` — instancia QueryClient configurada
3. `src/features/auth/types/auth.types.ts` — interfaces `Usuario`, `Rol`, `LoginRequest`, `AuthResponse`
4. `src/features/auth/store/authStore.ts` — Zustand store sin persist
5. `src/features/auth/api/authApi.ts` — funciones `login()`, `refresh()`, `logout()`, `me()` usando la instancia Axios
6. `src/features/auth/hooks/useLogin.ts` — `useMutation` que llama `authApi.login()` y actualiza Zustand
7. `src/features/auth/hooks/useLogout.ts` — `useMutation` que llama `authApi.logout()` y limpia Zustand
8. `src/features/auth/hooks/useMe.ts` — `useQuery` que llama `authApi.me()` para obtener datos del usuario actual
9. Un hook `useInitAuth.ts` que se ejecuta al montar la app e intenta el refresh silencioso
10. Barrel export en `src/features/auth/index.ts`
