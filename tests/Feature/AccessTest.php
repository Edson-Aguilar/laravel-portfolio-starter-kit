<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

it('redirects guests to login from the dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('allows admins to access user management', function () {
    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.users'))
        ->assertOk()
        ->assertSee('User management');
});

it('prevents users from accessing admin user management', function () {
    Role::firstOrCreate(['name' => 'user']);
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('admin.users'))
        ->assertForbidden();
});
