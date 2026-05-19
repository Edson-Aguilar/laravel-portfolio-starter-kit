# Uso Del Starter Kit

Guía práctica para usar el starter kit en un proyecto local Laravel con WSL, MySQL, Nginx, Livewire, Tailwind CSS, feature flags y API Sanctum.

## 1. Preparar Un Proyecto Local

Ejecuta el comando interactivo:

```bash
php artisan starter:setup
```

El comando pedirá:

- Nombre del proyecto.
- Dominio local `.test`.
- Nombre de la base de datos MySQL.
- Confirmación opcional para generar un archivo Nginx de ejemplo.

El comando realiza:

- Crea `.env` desde `.env.example` si no existe.
- Configura `APP_NAME`.
- Configura `APP_URL`.
- Configura `DB_CONNECTION=mysql`.
- Configura `DB_DATABASE`.
- Crea la base de datos MySQL si no existe.
- Ejecuta `php artisan key:generate`.
- Ejecuta `php artisan migrate --seed`.
- Ejecuta `php artisan storage:link`.
- Puede generar un archivo Nginx en `infra/nginx/{dominio}`.

El comando no modifica:

- `/etc/nginx/sites-available/`
- `/etc/nginx/sites-enabled/`
- `/mnt/c/Windows/System32/drivers/etc/hosts`

## 2. Configurar Nginx Local

Si generaste un archivo Nginx de ejemplo, quedará en:

```text
infra/nginx/tu-dominio.test
```

Para instalarlo manualmente en WSL:

```bash
sudo install -m 644 infra/nginx/tu-dominio.test /etc/nginx/sites-available/tu-dominio.test
sudo ln -sfn /etc/nginx/sites-available/tu-dominio.test /etc/nginx/sites-enabled/tu-dominio.test
sudo nginx -t
sudo service nginx reload
```

Después agrega manualmente el dominio al archivo hosts de Windows:

```text
127.0.0.1 tu-dominio.test
```

Ruta común:

```text
/mnt/c/Windows/System32/drivers/etc/hosts
```

## 3. Usar Las Cuentas Demo

Después de correr seeders:

| Rol | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Editor | `editor@example.com` | `password` |
| User | `user@example.com` | `password` |

URL local:

```text
http://tu-dominio.test/login
```

## 4. Crear Un CRUD Admin

Ejemplo:

```bash
php artisan make:admin-crud Product
```

Esto genera:

- `app/Models/Product.php`
- migración para `products`
- `database/factories/ProductFactory.php`
- `app/Policies/ProductPolicy.php`
- seeder de permisos `ProductPermissionSeeder`
- Livewire admin `ProductsIndex`
- vista admin
- vista Blade Livewire
- ruta `/admin/products`
- tests Pest básicos

Después revisa la migración generada y ejecuta:

```bash
php artisan migrate
php artisan db:seed --class=ProductPermissionSeeder
php artisan test
```

El CRUD generado usa los componentes visuales del starter:

- `x-admin.page-header`
- `x-admin.card`
- `x-admin.modal`
- `x-admin.table`
- `x-admin.input`
- `x-admin.select`
- `x-admin.badge`
- `x-admin.button`
- `x-admin.empty-state`
- `x-admin.confirm-modal`

## 5. Feature Flags

Los módulos se configuran en:

```text
config/starter.php
```

También se pueden controlar desde `.env`:

```dotenv
STARTER_MODULE_PROJECTS=true
STARTER_MODULE_APPEARANCE=true
STARTER_MODULE_ACTIVITY_LOG=false
STARTER_MODULE_API=true
STARTER_MODULE_EXPORTS=false
```

Módulos disponibles:

- `projects`: activa rutas, menú, dashboard y API de proyectos.
- `appearance`: activa el módulo de apariencia.
- `activity_log`: reservado para auditoría/eventos.
- `api`: activa endpoints API.
- `exports`: reservado para exportaciones.

Después de cambiar flags en `.env`, limpia configuración:

```bash
php artisan config:clear
php artisan cache:clear
```

## 6. API Con Sanctum

La API está disponible bajo:

```text
/api
```

### Login

```bash
curl -X POST http://tu-dominio.test/api/login \
  -H "Accept: application/json" \
  -d "email=admin@example.com" \
  -d "password=password" \
  -d "device_name=local"
```

Respuesta esperada:

```json
{
  "token_type": "Bearer",
  "access_token": "TOKEN",
  "abilities": ["user:read", "projects:read"]
}
```

### Usuario Autenticado

```bash
curl http://tu-dominio.test/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

### Proyectos Publicados

```bash
curl http://tu-dominio.test/api/projects \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

### Logout

```bash
curl -X POST http://tu-dominio.test/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

### Permisos Por Token

- `user:read`: permite consultar `/api/user`.
- `projects:read`: permite consultar `/api/projects` y `/api/projects/{project}`.

Los tokens de `admin` y `editor` reciben `projects:read` si el módulo `projects` está activo.

## 7. Testing Y Build

Tests:

```bash
php artisan test
```

Formato:

```bash
vendor/bin/pint
```

Build frontend:

```bash
npm run build
```

Auditorías:

```bash
composer audit
npm audit --audit-level=low
```

## 8. Troubleshooting

Permisos de Laravel:

```bash
chmod -R a+rwX storage bootstrap/cache
```

Storage link:

```bash
php artisan storage:link
```

Config cache:

```bash
php artisan config:clear
php artisan cache:clear
```

Nginx:

```bash
sudo nginx -t
sudo service nginx reload
```

MySQL:

- Revisa `DB_HOST`.
- Revisa `DB_PORT`.
- Revisa `DB_DATABASE`.
- Revisa `DB_USERNAME`.
- Revisa `DB_PASSWORD`.
- Confirma que el usuario tenga permisos para crear bases si usas `starter:setup`.
