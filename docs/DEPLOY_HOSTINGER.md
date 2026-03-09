# Despliegue en Hostinger — Laravel Inventario API

Guía paso a paso para desplegar la API en un hosting compartido de Hostinger con PHP 8.2+ y MySQL.

---

## Requisitos previos en Hostinger

Antes de empezar, verifica en el **hPanel** de Hostinger:

- PHP **8.2 o superior** (hPanel → Avanzado → Versión de PHP)
- Extensiones habilitadas: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `json`, `bcmath`, `ctype`, `fileinfo`, `xml`
- **Composer** disponible por SSH (o subir `vendor/` manualmente)
- Acceso **SSH** habilitado (hPanel → Avanzado → Acceso SSH)
- Base de datos **MySQL** creada (hPanel → Bases de datos → MySQL)

---

## Estructura de carpetas en Hostinger

Hostinger usa la carpeta `public_html` como raíz pública. Laravel necesita que **solo** `public/` quede expuesto. La estructura correcta es:

```
/home/u123456789/
├── public_html/          ← raíz pública del dominio
│   ├── index.php         ← apunta a ../laravel/public/index.php
│   ├── .htaccess         ← redirige todo a index.php
│   └── (archivos de public/ de Laravel)
└── laravel/              ← código fuente (fuera de public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── ...
```

> ⚠️ **Nunca** subas el código fuente dentro de `public_html/` directamente.
> El código de la app (app/, config/, .env) **no debe ser accesible** desde el navegador.

---

## Paso 1 — Preparar el proyecto localmente

```bash
# 1. Instalar dependencias sin paquetes de desarrollo
composer install --no-dev --optimize-autoloader

# 2. Verificar que el proyecto funciona en local
php artisan config:clear
php artisan route:list --path=api
```

---

## Paso 2 — Crear la base de datos en Hostinger

1. Ir a **hPanel → Bases de datos → MySQL**
2. Crear base de datos: `u123456789_inventario` (Hostinger prefija con tu usuario)
3. Crear usuario de BD y asignarle **todos los privilegios**
4. Anotar:
   - Host: `127.0.0.1`
   - Base de datos: `u123456789_inventario`
   - Usuario: `u123456789_dbuser`
   - Contraseña: la que definiste

---

## Paso 3 — Subir archivos por SSH / SFTP

### Opción A — Por SSH (recomendado)

```bash
# Conectarse por SSH (datos en hPanel → Avanzado → Acceso SSH)
ssh u123456789@tu-servidor.hostinger.com -p 65002

# Crear carpeta del proyecto fuera de public_html
mkdir ~/laravel
cd ~/laravel

# Clonar el repositorio
git clone https://github.com/MILLERMARRU/learn_laravel.git .

# Instalar dependencias (si Composer está disponible por SSH)
composer install --no-dev --optimize-autoloader
```

### Opción B — Por SFTP (FileZilla u otro cliente)

1. Subir todo el proyecto a `~/laravel/` (excepto las carpetas `node_modules/`, `.git/`)
2. Subir `vendor/` también (ya que puede no haber Composer en el servidor)

---

## Paso 4 — Configurar public_html

Conectado por SSH:

```bash
# Vaciar public_html (o hacer backup primero)
rm -rf ~/public_html/*

# Copiar el contenido de public/ de Laravel a public_html/
cp -r ~/laravel/public/. ~/public_html/

# Editar index.php para apuntar al bootstrap correcto
nano ~/public_html/index.php
```

Cambiar las rutas en `public_html/index.php`:

```php
// Línea original (aproximada):
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Cambiar a:
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

---

## Paso 5 — Crear el archivo .env

```bash
# Crear .env en el directorio del proyecto
cp ~/laravel/.env.example ~/laravel/.env
nano ~/laravel/.env
```

Configuración mínima para producción:

```env
APP_NAME="Inventario Licoreria"
APP_ENV=production
APP_KEY=                          # se genera en el paso 6
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_inventario
DB_USERNAME=u123456789_dbuser
DB_PASSWORD=tu_password_bd

# JWT
JWT_SECRET=                       # se genera en el paso 6
JWT_TTL=60
JWT_REFRESH_TTL=43200

# Admin inicial (para ProductionSeeder)
ADMIN_PASSWORD=TuPasswordMuySeguro123!
ADMIN_EMAIL=admin@tu-dominio.com

# Logs
LOG_CHANNEL=single
LOG_LEVEL=error
```

> ⚠️ **Nunca** subas el `.env` al repositorio de Git.

---

## Paso 6 — Generar claves

```bash
cd ~/laravel

# Generar APP_KEY de Laravel
php artisan key:generate

# Generar JWT secret
php artisan jwt:secret
```

Esto escribe automáticamente `APP_KEY` y `JWT_SECRET` en tu `.env`.

---

## Paso 7 — Permisos de carpetas

```bash
# Laravel necesita escribir en storage/ y bootstrap/cache/
chmod -R 775 ~/laravel/storage
chmod -R 775 ~/laravel/bootstrap/cache
chown -R tu-usuario:tu-usuario ~/laravel/storage
chown -R tu-usuario:tu-usuario ~/laravel/bootstrap/cache
```

---

## Paso 8 — Ejecutar migraciones y seeder

```bash
cd ~/laravel

# Ejecutar todas las migraciones
php artisan migrate --force

# Crear roles base y usuario administrador inicial
php artisan db:seed --class=ProductionSeeder
```

La consola mostrará las credenciales del admin:

```
✔  ProductionSeeder ejecutado correctamente.

+------------------------+----------------------------------+
| Campo                  | Valor                            |
+------------------------+----------------------------------+
| Rol                    | Administrador                    |
| Username               | admin                            |
| Email                  | admin@tu-dominio.com             |
| Password               | TuPasswordMuySeguro123!          |
| must_change_password   | true — cambia en el primer acceso|
+------------------------+----------------------------------+
⚠  Cambia la contraseña después del primer login.
```

---

## Paso 9 — Optimizar para producción

```bash
cd ~/laravel

# Cachear configuración, rutas y vistas
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

> Si modificas `.env` o rutas, debes limpiar el caché:
> ```bash
> php artisan config:clear && php artisan route:clear && php artisan cache:clear
> ```

---

## Paso 10 — Verificar que funciona

```bash
# Probar que la app responde
curl https://tu-dominio.com/up
# Debe retornar: OK

# Probar el login
curl -X POST https://tu-dominio.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"TuPasswordMuySeguro123!"}'
```

---

## Configuración del .htaccess

Si las rutas dan 404, verifica que `~/public_html/.htaccess` tenga este contenido:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Redirigir HTTP → HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Manejar Authorization header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirigir /index.php/... → /...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

> La redirección HTTP → HTTPS es importante para que las cookies `httpOnly`
> del refresh token funcionen con `SameSite=Strict; Secure`.

---

## Actualizaciones futuras

Cada vez que hagas cambios y quieras desplegar:

```bash
cd ~/laravel

# 1. Traer cambios del repo
git pull origin main

# 2. Actualizar dependencias si cambiaron
composer install --no-dev --optimize-autoloader

# 3. Ejecutar migraciones nuevas
php artisan migrate --force

# 4. Limpiar y regenerar caché
php artisan config:cache
php artisan route:cache
php artisan event:cache
```

---

## Verificación de seguridad antes de producción

- [ ] `APP_DEBUG=false` en `.env`
- [ ] `APP_ENV=production` en `.env`
- [ ] `.env` **no** está en el repositorio Git (en `.gitignore`)
- [ ] `storage/` y `bootstrap/cache/` con permisos 775
- [ ] Contraseña del admin cambiada tras el primer login
- [ ] HTTPS activo en el dominio
- [ ] `docs/` no accesible públicamente (Swagger solo en `APP_ENV=local`)

---

## Solución de problemas comunes

| Error | Causa | Solución |
|-------|-------|----------|
| `500 Internal Server Error` | `.env` mal configurado o sin `APP_KEY` | Revisar logs en `storage/logs/laravel.log` |
| `404 Not Found` en rutas API | `.htaccess` faltante o `mod_rewrite` desactivado | Verificar `.htaccess` en `public_html/` |
| `SQLSTATE: Access denied` | Credenciales de BD incorrectas | Verificar `DB_*` en `.env` |
| `JWT Secret not set` | Falta ejecutar `php artisan jwt:secret` | Ejecutar el comando y limpiar caché |
| Cookies de refresh token no llegan | HTTP sin HTTPS o SameSite incompatible | Activar SSL en hPanel y forzar HTTPS |
| `Permission denied` en storage | Permisos incorrectos | `chmod -R 775 ~/laravel/storage` |
