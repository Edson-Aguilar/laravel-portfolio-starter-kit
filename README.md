# Laravel Portfolio Starter Kit

Starter kit profesional para portafolios y paneles administrativos pequeños. Incluye autenticación, autorización por roles, dashboard, CRUD de usuarios, CRUD de proyectos con uploads seguros, filtros, seeders demo, tema visual configurable y pruebas Pest.

## Stack

- Laravel 13
- Livewire 4
- Tailwind CSS 4
- Spatie Laravel Permission
- MySQL
- Pest
- Vite

## Funcionalidades

- Dashboard administrativo responsive con soporte dark mode.
- Roles base: `admin`, `editor`, `user`.
- CRUD de usuarios protegido para `admin`.
- CRUD de proyectos protegido para `admin` y `editor`.
- Upload de imágenes de proyectos en disco `public` con validación de tipo, tamaño y dimensiones.
- Filtros y buscador en usuarios/proyectos.
- Módulo de apariencia para logo, colores del sistema y fuente.
- Seeders con cuentas demo listas para probar.
- Landing pública con proyectos publicados.
- Tests de acceso por rol, acciones Livewire, uploads y rutas protegidas.

## Cuentas Demo

Todas las cuentas usan la contraseña `password`.

| Rol | Email |
| --- | --- |
| Admin | `admin@example.com` |
| Editor | `editor@example.com` |
| User | `user@example.com` |

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
```

Servidor local con el script del proyecto:

```bash
composer run starter
```

Abre `/login` e inicia sesión con `admin@example.com` / `password`.

## WSL + Nginx

El proyecto incluye un server block de ejemplo en `infra/nginx/starter-kit.test`.

Dominio local:

```text
http://starter-kit.test
```

Entrada requerida en el archivo hosts de Windows:

```text
127.0.0.1 starter-kit.test
```

Instalar el server block en WSL:

```bash
sudo install -m 644 infra/nginx/starter-kit.test /etc/nginx/sites-available/starter-kit.test
sudo ln -sfn /etc/nginx/sites-available/starter-kit.test /etc/nginx/sites-enabled/starter-kit.test
sudo nginx -t
sudo service nginx reload
```

Permisos locales para que PHP-FPM pueda escribir cache, logs y sesiones:

```bash
chmod -R a+rwX storage bootstrap/cache
```

Variables importantes en `.env`:

```dotenv
APP_URL=http://starter-kit.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=starter_kit
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

## Seguridad Y Autorización

- Las rutas admin usan middleware `auth` y `role`.
- Las acciones Livewire críticas también autorizan en backend con policies.
- `admin` puede gestionar usuarios, proyectos y apariencia.
- `editor` puede gestionar proyectos, pero no usuarios ni apariencia.
- `user` solo accede al dashboard.
- Un admin no puede eliminar su propia cuenta ni dejar el sistema sin administradores.
- Los slugs de proyectos se normalizan y se validan como URLs limpias.
- Los uploads aceptan `jpg`, `jpeg`, `png` y `webp`, máximo 2 MB y dimensiones controladas.

## Tests

```bash
php artisan test
```

Ejecutar Pest directamente:

```bash
vendor/bin/pest
```

Los tests usan la base `starter_kit_testing` cuando está configurada en `phpunit.xml`.

## Roles Y Acceso

- `admin`: dashboard, CRUD de usuarios, CRUD de proyectos, apariencia.
- `editor`: dashboard, CRUD de proyectos.
- `user`: dashboard.

Los aliases de middleware de rol están configurados en `bootstrap/app.php`.

## Imágenes De Proyectos

Las imágenes se guardan en el disco `public`, dentro de `projects/`. Ejecuta `php artisan storage:link` después de instalar para exponerlas por `/storage`.

## Comandos Útiles

```bash
php artisan migrate:fresh --seed
composer run starter
php artisan test
vendor/bin/pint
npm run build
```

## Licencia

MIT
