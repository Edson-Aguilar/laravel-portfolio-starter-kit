<?php

use App\Models\SystemSetting;
use App\Models\User;
use App\Livewire\Admin\AppearanceSettings;
use App\Support\BrandTheme;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    SystemSetting::query()->delete();
    Cache::forget('brand-theme');
});

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

    expect($theme['brand_name'])->toBe('Laravel Admin Starter Kit')
        ->and($theme['primary'])->toBe('#2563eb')
        ->and($theme['surface'])->toBe('#ffffff')
        ->and($theme['font_family'])->toBe('Inter');
});

it('saves a supported font from appearance settings', function () {
    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(AppearanceSettings::class)
        ->set('brandName', 'Laravel Starter Kit')
        ->set('primary', '#2563eb')
        ->set('secondary', '#14b8a6')
        ->set('accent', '#f97316')
        ->set('surface', '#ffffff')
        ->set('darkSurface', '#09090b')
        ->set('fontFamily', 'Montserrat')
        ->call('save')
        ->assertHasNoErrors();

    expect(BrandTheme::get()['font_family'])->toBe('Montserrat');
});

it('rejects unsupported fonts from appearance settings', function () {
    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(AppearanceSettings::class)
        ->set('fontFamily', 'Papyrus')
        ->call('save')
        ->assertHasErrors(['fontFamily']);
});

it('exposes the selected font through css variables', function () {
    BrandTheme::put([
        'font_family' => 'Poppins',
    ]);

    expect(BrandTheme::cssVariables())->toContain('--brand-font: Poppins, ui-sans-serif, system-ui, sans-serif;');
});

it('applies the selected font variables on the public landing', function () {
    BrandTheme::put([
        'brand_name' => 'Laravel Starter Kit',
        'font_family' => 'Work Sans',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Laravel Starter Kit')
        ->assertSee('--brand-font: &quot;Work Sans&quot;, ui-sans-serif, system-ui, sans-serif;', false);
});
