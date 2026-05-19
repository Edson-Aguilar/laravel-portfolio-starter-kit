# Laravel Admin Starter Kit

Starter kit profesional para iniciar proyectos Laravel rápidamente. Incluye autenticación, autorización por roles, dashboard admin, CRUD de usuarios, proyectos demo con uploads seguros, filtros, seeders demo, tema visual configurable, API Sanctum, feature flags y pruebas Pest.

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
- Módulo de proyectos demo protegido para `admin` y `editor`, útil como referencia de CRUD.
- Upload de imágenes de proyectos demo en disco `public` con validación de tipo, tamaño y dimensiones.
- Filtros y buscador en usuarios y registros demo.
- Módulo de apariencia para logo, colores del sistema y fuente configurable.
- Feature flags para activar/desactivar módulos.
- API base con Laravel Sanctum.
- Comandos Artisan para setup local y generación de CRUDs admin.
- Seeders con cuentas demo listas para probar.
- Landing pública enfocada en el starter kit, comandos, API, tests, seguridad y módulos.
- Tests de acceso por rol, acciones Livewire, uploads y rutas protegidas.

## Capturas Y Features

El starter kit está preparado para documentar capturas del dashboard, CRUDs y módulos base. Sugerencia: agrega imágenes en `public/docs/` y enlázalas aquí.

Features principales para mostrar:

- Login y dashboard administrativo.
- Sidebar moderna con modo light/dark.
- CRUD de usuarios con roles.
- CRUD de proyectos demo con filtros, buscador y subida de imágenes.
- Módulo de apariencia para logo, paleta y fuente.
- Landing pública con enfoque de starter kit reutilizable.

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

Setup interactivo del starter:

```bash
php artisan starter:setup
```

El comando pregunta nombre del proyecto, dominio `.test`, base de datos MySQL, crea `.env` si hace falta, crea la base de datos, ejecuta `key:generate`, migraciones, seeders y `storage:link`. Puede generar un archivo Nginx de ejemplo en `infra/nginx/`, pero no toca `hosts` ni Nginx real.

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
- La API usa tokens Sanctum con abilities.

## Feature Flags

Los módulos se controlan desde `config/starter.php` y `.env`:

```dotenv
STARTER_MODULE_PROJECTS=true
STARTER_MODULE_APPEARANCE=true
STARTER_MODULE_ACTIVITY_LOG=false
STARTER_MODULE_API=true
STARTER_MODULE_EXPORTS=false
```

Cuando un módulo está desactivado, sus rutas y menús se ocultan o devuelven `404`.

## Branding, Colores Y Fuentes

El módulo `Apariencia` permite cambiar el nombre visual, logo, paleta y fuente del sistema desde el panel admin. Los valores se guardan como configuración en base de datos y se exponen al frontend mediante CSS variables:

- `--brand-primary`
- `--brand-secondary`
- `--brand-accent`
- `--brand-surface`
- `--brand-dark-surface`
- `--brand-font`

Fuentes disponibles:

- Inter
- Nunito
- Poppins
- Roboto
- Lato
- Montserrat
- Open Sans
- Source Sans 3
- Work Sans
- Manrope

Las fuentes se aplican con stacks CSS locales/sistema desde `app/Support/BrandTheme.php`. No se importa Google Fonts de forma remota, así que el build no depende de un proveedor externo. Si necesitas empaquetar fuentes reales, agrega los archivos al proyecto y ajusta `resources/css/app.css` con `@font-face`.

Para cambiar el branding base por código revisa:

```text
app/Support/BrandTheme.php
resources/views/welcome.blade.php
resources/views/components/layouts/admin.blade.php
```

## API

La API vive bajo `/api` y usa Laravel Sanctum.

Login con token:

```bash
curl -X POST http://starter-kit.test/api/login \
  -H "Accept: application/json" \
  -d "email=admin@example.com" \
  -d "password=password" \
  -d "device_name=local"
```

Usuario autenticado:

```bash
curl http://starter-kit.test/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"
```

Proyectos demo publicados:

```bash
curl http://starter-kit.test/api/projects \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"
```

Logout:

```bash
curl -X POST http://starter-kit.test/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"
```

Abilities incluidas:

- `user:read`: consultar `/api/user`.
- `projects:read`: consultar proyectos publicados. Solo se entrega a `admin` y `editor` cuando el módulo `projects` está activo.

Las rutas API usan `throttle:api`.

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

## Generar CRUD Admin

```bash
php artisan make:admin-crud Product
```

El comando genera:

- Model
- migration
- factory
- policy
- seeder de permisos Spatie
- componente Livewire admin
- blade admin usando los componentes del starter
- ruta admin
- tests Pest básicos

Después de generar un CRUD, revisa la migración, ejecuta:

```bash
php artisan migrate
php artisan db:seed --class=ProductPermissionSeeder
php artisan test
```

## Componentes Admin

Componentes Blade disponibles:

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

## Imágenes De Proyectos Demo

Las imágenes se guardan en el disco `public`, dentro de `projects/`. El modelo técnico sigue llamándose `Project` para evitar migraciones innecesarias, pero en la UI se presenta como proyectos demo o registros demo. Ejecuta `php artisan storage:link` después de instalar para exponerlas por `/storage`.

## Estructura Del Proyecto

```text
app/Livewire/Admin/        Componentes Livewire del dashboard y CRUDs
app/Console/Commands/      Comandos starter:setup y make:admin-crud
app/Http/Controllers/Api/  Controladores API Sanctum
app/Policies/              Autorización backend por rol
app/Support/BrandTheme.php Tema visual configurable
config/starter.php         Feature flags del starter
database/seeders/          Roles, usuarios demo y registros demo
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

### v1.1.0

- Reposiciona el producto como Laravel Admin Starter Kit reutilizable.
- Actualiza landing, dashboard, login y módulo de proyectos para usar lenguaje de starter kit.
- Amplía fuentes configurables y aplica `--brand-font` en landing, admin, guest, formularios, tablas y botones.
- Documenta branding, colores, fuentes y módulos demo.
- Agrega guía completa de uso del starter en `docs/USO_DEL_STARTER.md`.
- Agrega comando `starter:setup` para preparar proyectos locales con MySQL y dominio `.test`.
- Agrega comando `make:admin-crud` para generar CRUDs admin con Livewire, policy, permisos, migración, factory, vista y tests.
- Agrega feature flags desde `.env` y `config/starter.php` para módulos `projects`, `appearance`, `activity_log`, `api` y `exports`.
- Agrega API base con Laravel Sanctum, login por token, logout, `/api/user`, API Resource de proyectos, abilities y rate limiting.
- Extrae componentes Blade reutilizables para el admin y actualiza usuarios/proyectos para usarlos.
- Mejora el dashboard y las rutas para respetar módulos desactivados.
- Agrega tests de API y feature flags.

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
php artisan starter:setup
php artisan make:admin-crud Product
```

## Licencia

MIT
