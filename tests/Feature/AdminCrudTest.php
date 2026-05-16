<?php

use App\Livewire\Admin\ProjectsIndex;
use App\Livewire\Admin\UsersIndex;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['admin', 'editor', 'user'] as $role) {
        Role::firstOrCreate(['name' => $role]);
    }

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);
});

it('creates users with roles from the Livewire CRUD', function () {
    Livewire::test(UsersIndex::class)
        ->set('name', 'Jane Editor')
        ->set('email', 'jane@example.com')
        ->set('password', 'password')
        ->set('selectedRole', 'editor')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('editor'))->toBeTrue();
});

it('creates projects from the Livewire CRUD', function () {
    Livewire::test(ProjectsIndex::class)
        ->set('title', 'Portfolio CMS')
        ->set('slug', 'portfolio-cms')
        ->set('description', 'A polished Laravel portfolio admin.')
        ->set('projectStatus', 'published')
        ->call('save')
        ->assertHasNoErrors();

    expect(Project::where('slug', 'portfolio-cms')->where('status', 'published')->exists())->toBeTrue();
});
