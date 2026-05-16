<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['admin', 'editor', 'user'])->mapWithKeys(
            fn (string $role) => [$role => Role::firstOrCreate(['name' => $role])]
        );

        User::factory()
            ->create(['name' => 'Admin User', 'email' => 'admin@example.com'])
            ->assignRole($roles['admin']);

        User::factory()
            ->create(['name' => 'Editor User', 'email' => 'editor@example.com'])
            ->assignRole($roles['editor']);

        User::factory()
            ->create(['name' => 'Demo User', 'email' => 'user@example.com'])
            ->assignRole($roles['user']);

        User::factory(7)->create()->each(
            fn (User $user) => $user->assignRole($roles['user'])
        );

        Project::factory(12)->create();
    }
}
