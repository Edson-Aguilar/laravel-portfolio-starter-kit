<?php

namespace App\Support;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class BrandTheme
{
    public static function get(): array
    {
        return Cache::rememberForever('brand-theme', function (): array {
            $settings = SystemSetting::where('key', 'brand_theme')->first()?->value ?? [];

            return self::sanitize(array_replace(self::defaults(), array_filter($settings, fn ($value) => $value !== null && $value !== '')));
        });
    }

    public static function put(array $theme): void
    {
        SystemSetting::updateOrCreate(
            ['key' => 'brand_theme'],
            ['value' => self::sanitize(array_replace(self::defaults(), $theme))],
        );

        Cache::forget('brand-theme');
    }

    public static function defaults(): array
    {
        return [
            'brand_name' => 'Laravel Admin Starter Kit',
            'logo_path' => null,
            'primary' => '#2563eb',
            'secondary' => '#14b8a6',
            'accent' => '#f97316',
            'surface' => '#ffffff',
            'dark_surface' => '#09090b',
            'font_family' => 'Inter',
        ];
    }

    public static function fonts(): array
    {
        return array_keys(self::fontStacks());
    }

    public static function cssVariables(): string
    {
        $theme = self::get();

        return collect([
            '--brand-primary' => $theme['primary'],
            '--brand-secondary' => $theme['secondary'],
            '--brand-accent' => $theme['accent'],
            '--brand-surface' => $theme['surface'],
            '--brand-dark-surface' => $theme['dark_surface'],
            '--brand-font' => self::fontStack($theme['font_family']),
        ])->map(fn ($value, $key) => "{$key}: {$value};")->implode(' ');
    }

    public static function fontStack(string $font): string
    {
        return self::fontStacks()[$font] ?? self::fontStacks()[self::defaults()['font_family']];
    }

    private static function fontStacks(): array
    {
        return [
            'Inter' => 'Inter, ui-sans-serif, system-ui, sans-serif',
            'Nunito' => 'Nunito, ui-sans-serif, system-ui, sans-serif',
            'Poppins' => 'Poppins, ui-sans-serif, system-ui, sans-serif',
            'Roboto' => 'Roboto, Arial, ui-sans-serif, system-ui, sans-serif',
            'Lato' => 'Lato, ui-sans-serif, system-ui, sans-serif',
            'Montserrat' => 'Montserrat, ui-sans-serif, system-ui, sans-serif',
            'Open Sans' => '"Open Sans", ui-sans-serif, system-ui, sans-serif',
            'Source Sans 3' => '"Source Sans 3", ui-sans-serif, system-ui, sans-serif',
            'Work Sans' => '"Work Sans", ui-sans-serif, system-ui, sans-serif',
            'Manrope' => 'Manrope, ui-sans-serif, system-ui, sans-serif',
        ];
    }

    private static function sanitize(array $theme): array
    {
        $defaults = self::defaults();

        foreach (['primary', 'secondary', 'accent', 'surface', 'dark_surface'] as $key) {
            if (! is_string($theme[$key] ?? null) || ! preg_match('/^#[0-9A-Fa-f]{6}$/', $theme[$key])) {
                $theme[$key] = $defaults[$key];
            }
        }

        if (! in_array($theme['font_family'] ?? null, self::fonts(), true)) {
            $theme['font_family'] = $defaults['font_family'];
        }

        $theme['brand_name'] = trim((string) ($theme['brand_name'] ?? $defaults['brand_name'])) ?: $defaults['brand_name'];
        $theme['logo_path'] = filled($theme['logo_path'] ?? null) ? (string) $theme['logo_path'] : null;

        return $theme;
    }
}
