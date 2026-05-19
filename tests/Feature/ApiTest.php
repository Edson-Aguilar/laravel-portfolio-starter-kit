<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

function apiUserWithRole(string $role): User
{
    foreach (['admin', 'editor', 'user'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName]);
    }

    $user = User::factory()->create([
        'password' => 'password',
    ]);
    $user->assignRole($role);

    return $user;
}

it('issues api tokens with abilities for editors', function () {
    $editor = apiUserWithRole('editor');

    $this->postJson('/api/login', [
        'email' => $editor->email,
        'password' => 'password',
        'device_name' => 'pest',
    ])
        ->assertOk()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('abilities.0', 'user:read')
        ->assertJsonPath('abilities.1', 'projects:read')
        ->assertJsonStructure(['access_token']);
});

it('returns the authenticated api user with a valid token ability', function () {
    $user = apiUserWithRole('user');

    Sanctum::actingAs($user, ['user:read']);

    $this->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('email', $user->email);
});

it('lists published projects when the token can read projects', function () {
    $editor = apiUserWithRole('editor');
    $project = Project::factory()->create(['status' => 'published']);
    Project::factory()->create(['status' => 'draft']);

    Sanctum::actingAs($editor, ['user:read', 'projects:read']);

    $this->getJson('/api/projects')
        ->assertOk()
        ->assertJsonPath('data.0.slug', $project->slug)
        ->assertJsonCount(1, 'data');
});

it('blocks project api access without the project token ability', function () {
    $user = apiUserWithRole('user');

    Sanctum::actingAs($user, ['user:read']);

    $this->getJson('/api/projects')->assertForbidden();
});

it('hides api endpoints when the api module is disabled', function () {
    config(['starter.modules.api' => false]);

    $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertNotFound();
});
