<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsersIndex extends Component
{
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
        $this->resetForm();
    }

    public function edit(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRole = $user->roles()->first()?->name ?? 'user';
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
            'selectedRole' => ['required', Rule::exists('roles', 'name')],
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
            ],
        );

        $user->syncRoles([$data['selectedRole']]);

        session()->flash('status', 'User saved successfully.');
        $this->resetForm();
    }

    public function delete(int $userId): void
    {
        abort_if(auth()->id() === $userId, 403);

        User::findOrFail($userId)->delete();

        session()->flash('status', 'User deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password']);
        $this->selectedRole = 'user';
        $this->resetValidation();
    }

    public function render()
    {
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
