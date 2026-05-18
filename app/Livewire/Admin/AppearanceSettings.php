<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use App\Support\BrandTheme;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class AppearanceSettings extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public string $brandName = '';

    public string $primary = '';

    public string $secondary = '';

    public string $accent = '';

    public string $surface = '';

    public string $darkSurface = '';

    public string $fontFamily = 'Inter';

    public mixed $logo = null;

    public ?string $logoPath = null;

    public array $suggestedColors = [];

    public function mount(): void
    {
        $this->authorize('viewAny', SystemSetting::class);

        $theme = BrandTheme::get();

        $this->brandName = $theme['brand_name'];
        $this->primary = $theme['primary'];
        $this->secondary = $theme['secondary'];
        $this->accent = $theme['accent'];
        $this->surface = $theme['surface'];
        $this->darkSurface = $theme['dark_surface'];
        $this->fontFamily = $theme['font_family'];
        $this->logoPath = $theme['logo_path'];
    }

    public function updatedLogo(): void
    {
        $this->authorize('update', SystemSetting::class);

        $this->validate([
            'logo' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
        ]);

        $this->suggestedColors = $this->extractPalette($this->logo->getRealPath());

        if ($this->suggestedColors !== []) {
            $this->primary = $this->suggestedColors[0] ?? $this->primary;
            $this->secondary = $this->suggestedColors[1] ?? $this->secondary;
            $this->accent = $this->suggestedColors[2] ?? $this->accent;
        }
    }

    public function usePaletteColor(string $target, string $color): void
    {
        $this->authorize('update', SystemSetting::class);

        if (! in_array($target, ['primary', 'secondary', 'accent', 'surface', 'darkSurface'], true)) {
            return;
        }

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return;
        }

        $this->{$target} = $color;
    }

    public function save(): void
    {
        $this->authorize('update', SystemSetting::class);

        $data = $this->validate([
            'brandName' => ['required', 'string', 'max:80'],
            'primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'surface' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'darkSurface' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'fontFamily' => ['required', Rule::in(['Inter', 'Manrope', 'Plus Jakarta Sans', 'Nunito Sans'])],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
        ]);

        $previousLogoPath = $this->logoPath;
        $newLogoPath = null;

        if ($this->logo) {
            $newLogoPath = $this->logo->store('branding', 'public');
            $this->logoPath = $newLogoPath;
        }

        try {
            DB::transaction(function () use ($data): void {
                BrandTheme::put([
                    'brand_name' => $data['brandName'],
                    'logo_path' => $this->logoPath,
                    'primary' => $data['primary'],
                    'secondary' => $data['secondary'],
                    'accent' => $data['accent'],
                    'surface' => $data['surface'],
                    'dark_surface' => $data['darkSurface'],
                    'font_family' => $data['fontFamily'],
                ]);
            });
        } catch (Throwable $exception) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            $this->logoPath = $previousLogoPath;

            throw $exception;
        }

        if ($newLogoPath && $previousLogoPath && $newLogoPath !== $previousLogoPath) {
            Storage::disk('public')->delete($previousLogoPath);
        }

        session()->flash('status', 'Apariencia guardada correctamente.');
        $this->logo = null;
    }

    private function extractPalette(string $path): array
    {
        $info = getimagesize($path);

        if (! $info) {
            return [];
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $image) {
            return [];
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $colors = [];
        $step = max(1, (int) floor(max($width, $height) / 50));

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 255;
                $g = ($rgb >> 8) & 255;
                $b = $rgb & 255;

                if ($this->isUsefulColor($r, $g, $b)) {
                    $key = sprintf('%02x%02x%02x', (int) round($r / 24) * 24, (int) round($g / 24) * 24, (int) round($b / 24) * 24);
                    $colors[$key] = ($colors[$key] ?? 0) + 1;
                }
            }
        }

        imagedestroy($image);
        arsort($colors);

        return collect(array_keys($colors))
            ->take(8)
            ->map(fn (string $color) => '#'.str_pad(substr($color, 0, 6), 6, '0'))
            ->values()
            ->all();
    }

    private function isUsefulColor(int $r, int $g, int $b): bool
    {
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        return $max - $min > 18 && $max > 35 && $min < 245;
    }

    public function render()
    {
        $this->authorize('viewAny', SystemSetting::class);

        return view('livewire.admin.appearance-settings', [
            'fonts' => ['Inter', 'Manrope', 'Plus Jakarta Sans', 'Nunito Sans'],
        ]);
    }
}
