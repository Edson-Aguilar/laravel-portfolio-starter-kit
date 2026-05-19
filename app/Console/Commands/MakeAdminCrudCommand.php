<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAdminCrudCommand extends Command
{
    protected $signature = 'make:admin-crud {name}';

    protected $description = 'Genera un CRUD admin Livewire alineado al starter kit.';

    public function handle(): int
    {
        $name = Str::studly((string) $this->argument('name'));
        $variable = Str::camel($name);
        $plural = Str::pluralStudly($name);
        $pluralVariable = Str::camel($plural);
        $table = Str::snake(Str::plural($name));
        $route = Str::kebab(Str::plural($name));
        $title = Str::headline($plural);

        $this->writeFile(app_path("Models/{$name}.php"), $this->modelStub($name));
        $this->writeFile(database_path('migrations/'.$this->timestamp()."_create_{$table}_table.php"), $this->migrationStub($table));
        $this->writeFile(database_path("factories/{$name}Factory.php"), $this->factoryStub($name));
        $this->writeFile(app_path("Policies/{$name}Policy.php"), $this->policyStub($name, $route));
        $this->writeFile(database_path("seeders/{$name}PermissionSeeder.php"), $this->permissionSeederStub($name, $route));
        $this->writeFile(app_path("Livewire/Admin/{$plural}Index.php"), $this->livewireStub($name, $plural, $variable, $pluralVariable));
        $this->writeFile(resource_path("views/admin/{$route}.blade.php"), $this->adminViewStub($plural));
        $this->writeFile(resource_path("views/livewire/admin/{$route}-index.blade.php"), $this->bladeStub($title, $variable, $pluralVariable));
        $this->writeFile(base_path("tests/Feature/{$plural}AdminTest.php"), $this->testStub($name, $plural, $route));
        $this->appendRoute($route, $plural);

        $this->info("CRUD admin generado: {$title}");
        $this->line("Ruta: /admin/{$route}");
        $this->line("Permisos generados en seeder: database/seeders/{$name}PermissionSeeder.php");

        return self::SUCCESS;
    }

    private function writeFile(string $path, string $contents): void
    {
        File::ensureDirectoryExists(dirname($path));

        if (File::exists($path) && ! $this->confirm("{$path} ya existe. ¿Sobrescribir?", false)) {
            $this->warn("Omitido: {$path}");

            return;
        }

        File::put($path, $contents);
    }

    private function appendRoute(string $route, string $plural): void
    {
        $path = base_path('routes/web.php');
        $content = File::get($path);
        $line = "Route::view('/admin/{$route}', 'admin.{$route}')->middleware(['auth', 'role:admin'])->name('admin.{$route}');";

        if (str_contains($content, $line)) {
            return;
        }

        $content = str_replace('// starter:admin-routes', $line.PHP_EOL.'// starter:admin-routes', $content);
        File::put($path, $content);
    }

    private function timestamp(): string
    {
        return now()->format('Y_m_d_His');
    }

    private function modelStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Database\Factories\\{$name}Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]
class {$name} extends Model
{
    /** @use HasFactory<{$name}Factory> */
    use HasFactory;
}
PHP;
    }

    private function migrationStub(string $table): string
    {
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table): void {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    private function factoryStub(string $name): string
    {
        return <<<PHP
<?php

namespace Database\Factories;

use App\Models\\{$name};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<{$name}>
 */
class {$name}Factory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
PHP;
    }

    private function policyStub(string $name, string $route): string
    {
        return <<<PHP
<?php

namespace App\Policies;

use App\Models\\{$name};
use App\Models\User;

class {$name}Policy
{
    public function viewAny(User \$user): bool
    {
        return \$user->hasRole('admin') || \$user->can('{$route}.view');
    }

    public function create(User \$user): bool
    {
        return \$user->hasRole('admin') || \$user->can('{$route}.create');
    }

    public function update(User \$user, {$name} \${$this->camel($name)}): bool
    {
        return \${$this->camel($name)}->exists && (\$user->hasRole('admin') || \$user->can('{$route}.update'));
    }

    public function delete(User \$user, {$name} \${$this->camel($name)}): bool
    {
        return \${$this->camel($name)}->exists && (\$user->hasRole('admin') || \$user->can('{$route}.delete'));
    }
}
PHP;
    }

    private function permissionSeederStub(string $name, string $route): string
    {
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class {$name}PermissionSeeder extends Seeder
{
    public function run(): void
    {
        \$permissions = collect(['view', 'create', 'update', 'delete'])
            ->map(fn (string \$action) => Permission::firstOrCreate(['name' => '{$route}.'.\$action]));

        Role::firstOrCreate(['name' => 'admin'])->givePermissionTo(\$permissions);
    }
}
PHP;
    }

    private function livewireStub(string $name, string $plural, string $variable, string $pluralVariable): string
    {
        $view = Str::kebab($plural).'-index';

        return <<<PHP
<?php

namespace App\Livewire\Admin;

use App\Models\\{$name};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class {$plural}Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string \$search = '';

    public ?int \$editingId = null;

    public string \$name = '';

    public string \$description = '';

    public ?int \$confirmingDeleteId = null;

    public bool \$showForm = false;

    public function mount(): void
    {
        \$this->authorize('viewAny', {$name}::class);
    }

    public function updatingSearch(): void
    {
        \$this->resetPage();
    }

    public function create(): void
    {
        \$this->authorize('create', {$name}::class);
        \$this->resetForm();
        \$this->showForm = true;
    }

    public function edit(int \$id): void
    {
        \${$variable} = {$name}::findOrFail(\$id);
        \$this->authorize('update', \${$variable});

        \$this->editingId = \${$variable}->id;
        \$this->name = \${$variable}->name;
        \$this->description = \${$variable}->description ?? '';
        \$this->showForm = true;
    }

    public function save(): void
    {
        \${$variable} = \$this->editingId ? {$name}::findOrFail(\$this->editingId) : null;
        \$this->authorize(\${$variable} ? 'update' : 'create', \${$variable} ?? {$name}::class);

        \$data = \$this->validate([
            'name' => ['required', 'string', 'min:2', 'max:160', Rule::unique('{$this->snakePlural($name)}', 'name')->ignore(\$this->editingId)],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        {$name}::updateOrCreate(['id' => \$this->editingId], \$data);

        session()->flash('status', '{$name} guardado correctamente.');
        \$this->resetForm();
    }

    public function confirmDelete(int \$id): void
    {
        \$this->authorize('delete', {$name}::findOrFail(\$id));
        \$this->confirmingDeleteId = \$id;
    }

    public function delete(): void
    {
        if (! \$this->confirmingDeleteId) {
            return;
        }

        \${$variable} = {$name}::findOrFail(\$this->confirmingDeleteId);
        \$this->authorize('delete', \${$variable});
        \${$variable}->delete();

        session()->flash('status', '{$name} eliminado correctamente.');
        \$this->confirmingDeleteId = null;
    }

    public function resetForm(): void
    {
        \$this->reset(['editingId', 'name', 'description']);
        \$this->showForm = false;
        \$this->resetValidation();
    }

    public function render()
    {
        \$this->authorize('viewAny', {$name}::class);

        return view('livewire.admin.{$view}', [
            '{$pluralVariable}' => {$name}::query()
                ->when(\$this->search, fn (\$query) => \$query->where('name', 'like', "%{\$this->search}%"))
                ->latest()
                ->paginate(10),
        ]);
    }
}
PHP;
    }

    private function adminViewStub(string $plural): string
    {
        return <<<BLADE
<x-layouts.admin title="{$plural}">
    <livewire:admin.{$this->kebab($plural)}-index />
</x-layouts.admin>
BLADE;
    }

    private function bladeStub(string $title, string $variable, string $pluralVariable): string
    {
        return <<<BLADE
<div class="space-y-6">
    <x-admin.card class="relative min-w-0 overflow-hidden">
        <x-admin.skeleton-table />
        <x-admin.page-header eyebrow="Administración" title="{$title}" description="Gestiona registros desde el panel admin.">
            <x-slot:actions>
                <x-admin.button wire:click="create" wire:loading.attr="disabled" wire:target="create,save,delete">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nuevo
                </x-admin.button>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="border-b border-zinc-100 p-5 dark:border-white/10">
            <x-admin.input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar" />
        </div>

        <x-admin.table>
            <thead>
                <tr>
                    <th class="text-left font-bold">Nombre</th>
                    <th class="text-left font-bold">Descripción</th>
                    <th class="text-right font-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse (\${$pluralVariable} as \${$variable})
                    <tr wire:key="{$variable}-{{ \${$variable}->id }}" class="transition hover:bg-zinc-50/80 dark:hover:bg-white/5">
                        <td class="font-semibold text-zinc-950 dark:text-white">{{ \${$variable}->name }}</td>
                        <td class="text-zinc-600 dark:text-zinc-300">{{ str(\${$variable}->description)->limit(80) }}</td>
                        <td class="text-right">
                            <x-admin.button variant="secondary" wire:click="edit({{ \${$variable}->id }})" class="mr-2 px-3 py-1.5">Editar</x-admin.button>
                            <x-admin.button variant="danger" wire:click="confirmDelete({{ \${$variable}->id }})" class="px-3 py-1.5">Eliminar</x-admin.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <x-admin.empty-state title="No hay registros" description="Crea el primer registro para iniciar." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-admin.table>

        <div class="border-t border-zinc-100 px-5 py-4 dark:border-white/10">
            {{ \${$pluralVariable}->links() }}
        </div>
    </x-admin.card>

    <x-admin.modal :show="\$showForm" id="{$variable}-form" :title="\$editingId ? 'Editar registro' : 'Crear registro'" description="Completa la información requerida." class="max-w-xl">
        <x-slot:close>
            <x-admin.button variant="icon" wire:click="resetForm" aria-label="Cerrar formulario">
                <x-icon name="x-mark" class="h-5 w-5" />
            </x-admin.button>
        </x-slot:close>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Nombre</label>
                <x-admin.input wire:model="name" class="mt-2" />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ \$message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Descripción</label>
                <textarea wire:model="description" rows="4" class="form-control mt-2"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ \$message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">Guardar</x-admin.button>
                <x-admin.button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled" wire:target="save">Cancelar</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.confirm-modal
        :show="\$confirmingDeleteId !== null"
        title="Eliminar registro"
        description="Este registro se eliminará permanentemente."
        confirm="Eliminar"
        action="delete"
    />
</div>
BLADE;
    }

    private function testStub(string $name, string $plural, string $route): string
    {
        return <<<PHP
<?php

use App\Livewire\Admin\\{$plural}Index;
use App\Models\\{$name};
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('allows admins to access {$route} admin page', function () {
    Role::firstOrCreate(['name' => 'admin']);
    \$admin = User::factory()->create();
    \$admin->assignRole('admin');

    \$this->actingAs(\$admin)->get(route('admin.{$route}'))->assertOk();
});

it('creates {$route} records from Livewire', function () {
    Role::firstOrCreate(['name' => 'admin']);
    \$admin = User::factory()->create();
    \$admin->assignRole('admin');

    Livewire::actingAs(\$admin)
        ->test({$plural}Index::class)
        ->set('name', 'Demo {$name}')
        ->set('description', 'Generated admin CRUD record.')
        ->call('save')
        ->assertHasNoErrors();

    expect({$name}::where('name', 'Demo {$name}')->exists())->toBeTrue();
});
PHP;
    }

    private function camel(string $value): string
    {
        return Str::camel($value);
    }

    private function kebab(string $value): string
    {
        return Str::kebab($value);
    }

    private function snakePlural(string $value): string
    {
        return Str::snake(Str::plural($value));
    }
}
