<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

it('hides projects admin route when projects module is disabled', function () {
    config(['starter.modules.projects' => false]);

    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin/projects')
        ->assertNotFound();
});

it('hides appearance admin route when appearance module is disabled', function () {
    config(['starter.modules.appearance' => false]);

    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin/appearance')
        ->assertNotFound();
});
