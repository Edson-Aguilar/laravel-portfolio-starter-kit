<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PDO;
use RuntimeException;

class StarterSetupCommand extends Command
{
    protected $signature = 'starter:setup';

    protected $description = 'Configura el starter kit localmente con MySQL y dominio .test.';

    public function handle(): int
    {
        $projectName = (string) $this->ask('Nombre del proyecto', 'Portfolio Kit');
        $domain = $this->normalizeDomain((string) $this->ask('Dominio local .test', 'starter-kit.test'));
        $database = $this->normalizeDatabase((string) $this->ask('Base de datos MySQL', 'starter_kit'));

        $this->prepareEnvironmentFile($projectName, $domain, $database);
        $this->createDatabase($database);

        $this->call('key:generate', ['--force' => true]);
        $this->call('migrate', ['--seed' => true, '--force' => true]);
        $this->call('storage:link');

        if ($this->confirm('¿Quieres generar un archivo Nginx de ejemplo en infra/nginx?', true)) {
            $this->writeNginxExample($domain);
        }

        $this->newLine();
        $this->info('Starter configurado correctamente.');
        $this->line("URL local: http://{$domain}");
        $this->line('Credenciales demo: admin@example.com / password');
        $this->newLine();
        $this->warn('No modifiqué hosts ni Nginx real.');
        $this->line("Si quieres usar el dominio, agrega manualmente: 127.0.0.1 {$domain}");
        $this->line('Archivo hosts Windows: /mnt/c/Windows/System32/drivers/etc/hosts');
        $this->line("Nginx sugerido: infra/nginx/{$domain}");

        return self::SUCCESS;
    }

    private function prepareEnvironmentFile(string $projectName, string $domain, string $database): void
    {
        if (! File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
        }

        $content = File::get(base_path('.env'));
        $content = $this->setEnvValue($content, 'APP_NAME', $projectName);
        $content = $this->setEnvValue($content, 'APP_URL', "http://{$domain}");
        $content = $this->setEnvValue($content, 'DB_CONNECTION', 'mysql');
        $content = $this->setEnvValue($content, 'DB_DATABASE', $database);

        File::put(base_path('.env'), $content);
    }

    private function createDatabase(string $database): void
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            str_replace('`', '``', $database)
        ));
    }

    private function writeNginxExample(string $domain): void
    {
        $path = base_path("infra/nginx/{$domain}");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, <<<NGINX
server {
    listen 80;
    server_name {$domain};
    root {$this->laravelPublicPath()};

    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param TMPDIR /tmp;
    }
}
NGINX);
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain) ?: $domain;
        $domain = trim($domain, '/');

        if (! str_ends_with($domain, '.test')) {
            $domain .= '.test';
        }

        return $domain;
    }

    private function normalizeDatabase(string $database): string
    {
        $database = trim($database);

        if (! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
            throw new RuntimeException('La base de datos solo puede contener letras, numeros y guion bajo.');
        }

        return $database;
    }

    private function setEnvValue(string $content, string $key, string $value): string
    {
        $escaped = str_contains($value, ' ') ? '"'.$value.'"' : $value;

        if (preg_match("/^{$key}=.*$/m", $content)) {
            return preg_replace("/^{$key}=.*$/m", "{$key}={$escaped}", $content) ?? $content;
        }

        return rtrim($content).PHP_EOL."{$key}={$escaped}".PHP_EOL;
    }

    private function laravelPublicPath(): string
    {
        return base_path('public');
    }
}
