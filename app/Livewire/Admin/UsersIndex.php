<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsersIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $role = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $selectedRole = 'user';

    public ?int $confirmingDeleteId = null;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('create', User::class);

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->authorize('update', $user);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRole = $user->roles()->first()?->name ?? 'user';
        $this->showForm = true;
    }

    public function save(): void
    {
        $target = $this->editingId ? User::findOrFail($this->editingId) : null;

        $this->authorize($target ? 'update' : 'create', $target ?? User::class);

        $data = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8', 'max:128'],
            'selectedRole' => ['required', Rule::in(['admin', 'editor', 'user']), Rule::exists('roles', 'name')],
        ]);

        $this->ensureRoleCanBeAssigned($target, $data['selectedRole']);

        try {
            DB::transaction(function () use ($data): void {
                $user = User::updateOrCreate(
                    ['id' => $this->editingId],
                    [
                        'name' => $data['name'],
                        'email' => str($data['email'])->lower()->toString(),
                        ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
                    ],
                );

                $user->syncRoles([$data['selectedRole']]);
            });
        } catch (QueryException $exception) {
            $this->throwEmailCollisionIfNeeded($exception);

            throw $exception;
        }

        session()->flash('status', 'Usuario guardado correctamente.');
        $this->resetForm();
    }

    public function confirmDelete(int $userId): void
    {
        $this->authorize('delete', User::findOrFail($userId));

        $this->confirmingDeleteId = $userId;
    }

    public function delete(): void
    {
        $userId = $this->confirmingDeleteId;

        if (! $userId) {
            return;
        }

        $user = User::findOrFail($userId);

        $this->authorize('delete', $user);

        $user->delete();

        session()->flash('status', 'Usuario eliminado correctamente.');
        $this->confirmingDeleteId = null;
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password']);
        $this->selectedRole = 'user';
        $this->showForm = false;
        $this->resetValidation();
    }

    private function ensureRoleCanBeAssigned(?User $target, string $role): void
    {
        if (! $target || $role === 'admin' || ! $target->hasRole('admin')) {
            return;
        }

        $isSelfDemotion = $target->is(auth()->user());
        $hasAnotherAdmin = User::role('admin')->whereKeyNot($target->getKey())->exists();

        if ($isSelfDemotion || ! $hasAnotherAdmin) {
            throw ValidationException::withMessages([
                'selectedRole' => 'Debe existir al menos un administrador activo y no puedes quitarte tu propio rol de admin.',
            ]);
        }
    }

    private function throwEmailCollisionIfNeeded(QueryException $exception): void
    {
        if ((string) $exception->getCode() !== '23000') {
            return;
        }

        throw ValidationException::withMessages([
            'email' => 'Este email ya está registrado.',
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        return view('livewire.admin.users-index', [
            'roles' => Role::orderBy('name')->get(),
            'users' => User::query()
                ->with('roles')
                ->when($this->search, fn ($query) => $query->where(function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                }))
                ->when($this->role, fn ($query) => $query->role($this->role))
                ->latest()
                ->paginate(10),
        ]);
    }
}
