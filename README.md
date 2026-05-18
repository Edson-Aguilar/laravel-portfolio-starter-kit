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

## Capturas Y Features

El starter kit está preparado para mostrar capturas del portafolio en esta sección. Sugerencia para tu portafolio: agrega imágenes en `public/docs/` y enlázalas aquí.

Features principales para mostrar:

- Login y dashboard administrativo.
- Sidebar moderna con modo light/dark.
- CRUD de usuarios con roles.
- CRUD de proyectos con filtros, buscador y subida de imágenes.
- Módulo de apariencia para logo, paleta y fuente.
- Landing pública con proyectos publicados.

## Cuentas Demo

Todas las cuentas usan la contraseña `password`.

| Rol | Email |
| --- | --- |
| Admin | `admin@example.com` |
| Editor | `editor@example.com` |
| User | `user@example.com` |

## Instalación

Requisitos:

- PHP 8.3+
- Composer
- Node.js y npm
- MySQL
- Nginx con PHP-FPM si usarás el dominio `.test`

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

Base de datos de tests recomendada:

```dotenv
DB_DATABASE=starter_kit_testing
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

## Build Frontend

Compilar assets para producción:

```bash
npm run build
```

Durante desarrollo con Vite:

```bash
npm run dev
```

## Roles Y Acceso

- `admin`: dashboard, CRUD de usuarios, CRUD de proyectos, apariencia.
- `editor`: dashboard, CRUD de proyectos.
- `user`: dashboard.

Los aliases de middleware de rol están configurados en `bootstrap/app.php`.

## Imágenes De Proyectos

Las imágenes se guardan en el disco `public`, dentro de `projects/`. Ejecuta `php artisan storage:link` después de instalar para exponerlas por `/storage`.

## Estructura Del Proyecto

```text
app/Livewire/Admin/        Componentes Livewire del dashboard y CRUDs
app/Policies/              Autorización backend por rol
app/Support/BrandTheme.php Tema visual configurable
database/seeders/          Roles, usuarios demo y proyectos demo
resources/views/admin/     Entradas de vistas del panel admin
resources/views/livewire/  Vistas Livewire
resources/css/app.css      Estilos Tailwind y componentes UI
routes/web.php             Rutas públicas, auth y admin
tests/Feature/             Tests Pest de acceso, CRUD, uploads y tema
infra/nginx/               Server block local para starter-kit.test
```

## Troubleshooting

- Error de permisos en `storage` o `bootstrap/cache`:

```bash
chmod -R a+rwX storage bootstrap/cache
```

- Imágenes no visibles:

```bash
php artisan storage:link
```

- Cambios de `.env` no aplican:

```bash
php artisan config:clear
php artisan cache:clear
```

- Nginx no carga el sitio:

```bash
sudo nginx -t
sudo service nginx reload
```

- MySQL no conecta:

Revisa `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`. Para tests, confirma que exista la base `starter_kit_testing`.

## Changelog

### v1.0.0

- Starter kit Laravel con Livewire, Tailwind CSS, Spatie Permission y Pest.
- Dashboard admin con roles `admin`, `editor`, `user`.
- CRUD de usuarios y proyectos con autorización backend.
- Uploads seguros y transaccionales para proyectos y logo.
- Módulo de apariencia con logo, colores sugeridos y fuente.
- UI responsive con dark mode, sidebar colapsable, empty states, skeleton loaders, toasts y modales.
- Tests de roles, accesos directos, CRUD, uploads, tema y protecciones del último admin.
- Configuración local WSL con MySQL y Nginx para `starter-kit.test`.

## Comandos Útiles

```bash
php artisan migrate:fresh --seed
composer run starter
php artisan test
vendor/bin/pint
npm run build
composer audit
npm audit --audit-level=low
```

## Licencia

MIT
