<?php

use App\Models\SystemSetting;
use App\Support\BrandTheme;
use Illuminate\Support\Facades\Cache;

it('sanitizes invalid theme values when reading from storage', function () {
    SystemSetting::create([
        'key' => 'brand_theme',
        'value' => [
            'brand_name' => '',
            'primary' => 'red; background: black',
            'secondary' => '#14b8a6',
            'accent' => '#f97316',
            'surface' => 'transparent',
            'dark_surface' => '#09090b',
            'font_family' => 'Papyrus',
        ],
    ]);

    Cache::forget('brand-theme');

    $theme = BrandTheme::get();

    expect($theme['brand_name'])->toBe('Portfolio Kit')
        ->and($theme['primary'])->toBe('#2563eb')
        ->and($theme['surface'])->toBe('#ffffff')
        ->and($theme['font_family'])->toBe('Inter');
});
