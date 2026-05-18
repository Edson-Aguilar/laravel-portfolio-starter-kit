<?php

use App\Livewire\Admin\AppearanceSettings;
use App\Livewire\Admin\ProjectsIndex;
use App\Livewire\Admin\UsersIndex;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function userWithRole(string $role): User
{
    foreach (['admin', 'editor', 'user'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName]);
    }

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

it('allows editors to manage projects but blocks user management', function () {
    $editor = userWithRole('editor');

    $this->actingAs($editor)
        ->get(route('admin.projects'))
        ->assertOk();

    $this->actingAs($editor)
        ->get(route('admin.users'))
        ->assertForbidden();

    Livewire::actingAs($editor)
        ->test(ProjectsIndex::class)
        ->set('title', 'Editor Project')
        ->set('slug', 'editor-project')
        ->set('projectStatus', 'published')
        ->call('save')
        ->assertHasNoErrors();

    expect(Project::where('slug', 'editor-project')->exists())->toBeTrue();
});

it('blocks normal users from admin project routes and direct Livewire calls', function () {
    $user = userWithRole('user');

    $this->actingAs($user)
        ->get(route('admin.projects'))
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(ProjectsIndex::class)
        ->assertForbidden();
});

it('blocks editors from direct Livewire user and appearance management', function () {
    $editor = userWithRole('editor');

    Livewire::actingAs($editor)
        ->test(UsersIndex::class)
        ->assertForbidden();

    Livewire::actingAs($editor)
        ->test(AppearanceSettings::class)
        ->assertForbidden();
});

it('prevents admins from deleting themselves or the last admin account', function () {
    $admin = userWithRole('admin');

    Livewire::actingAs($admin)
        ->test(UsersIndex::class)
        ->call('confirmDelete', $admin->id)
        ->assertForbidden();

    expect(User::query()->count())->toBe(1);
});

it('prevents admins from removing their own admin role even when another admin exists', function () {
    $admin = userWithRole('admin');
    userWithRole('admin');

    Livewire::actingAs($admin)
        ->test(UsersIndex::class)
        ->call('edit', $admin->id)
        ->set('selectedRole', 'editor')
        ->call('save')
        ->assertHasErrors(['selectedRole']);

    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
});

it('prevents removing the last admin role', function () {
    $admin = userWithRole('admin');

    Livewire::actingAs($admin)
        ->test(UsersIndex::class)
        ->call('edit', $admin->id)
        ->set('selectedRole', 'user')
        ->call('save')
        ->assertHasErrors(['selectedRole']);

    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
});

it('allows an admin to demote another admin when at least one admin remains', function () {
    $admin = userWithRole('admin');
    $otherAdmin = userWithRole('admin');

    Livewire::actingAs($admin)
        ->test(UsersIndex::class)
        ->call('edit', $otherAdmin->id)
        ->set('selectedRole', 'editor')
        ->call('save')
        ->assertHasNoErrors();

    expect($otherAdmin->fresh()->hasRole('editor'))->toBeTrue()
        ->and($admin->fresh()->hasRole('admin'))->toBeTrue();
});

it('blocks normal users from appearance routes by direct URL', function () {
    $user = userWithRole('user');

    $this->actingAs($user)
        ->get(route('admin.appearance'))
        ->assertForbidden();
});
