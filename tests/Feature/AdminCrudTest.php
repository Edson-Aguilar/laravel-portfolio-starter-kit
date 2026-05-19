<?php

use App\Livewire\Admin\AppearanceSettings;
use App\Livewire\Admin\ProjectsIndex;
use App\Livewire\Admin\UsersIndex;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        ->set('title', 'Demo Module')
        ->set('slug', 'demo-module')
        ->set('description', 'A polished Laravel admin starter module.')
        ->set('projectStatus', 'published')
        ->call('save')
        ->assertHasNoErrors();

    expect(Project::where('slug', 'demo-module')->where('status', 'published')->exists())->toBeTrue();
});

it('stores project images only on the public disk when the upload is valid', function () {
    Storage::fake('public');

    Livewire::test(ProjectsIndex::class)
        ->set('title', 'Image Project')
        ->set('slug', 'image-project')
        ->set('projectStatus', 'draft')
        ->set('image', UploadedFile::fake()->image('cover.webp', 900, 600))
        ->call('save')
        ->assertHasNoErrors();

    $project = Project::where('slug', 'image-project')->firstOrFail();

    expect($project->image_path)->toStartWith('projects/');
    Storage::disk('public')->assertExists($project->image_path);
});

it('rejects non image project uploads', function () {
    Storage::fake('public');

    Livewire::test(ProjectsIndex::class)
        ->set('title', 'Unsafe Upload')
        ->set('slug', 'unsafe-upload')
        ->set('projectStatus', 'draft')
        ->set('image', UploadedFile::fake()->image('payload.gif', 600, 400))
        ->call('save')
        ->assertHasErrors(['image']);

    expect(Project::where('slug', 'unsafe-upload')->exists())->toBeFalse();
});

it('replaces project images only after the project is saved', function () {
    Storage::fake('public');

    $oldPath = UploadedFile::fake()->image('old.jpg', 600, 400)->store('projects', 'public');
    $project = Project::factory()->create([
        'slug' => 'replace-image',
        'image_path' => $oldPath,
    ]);

    Livewire::test(ProjectsIndex::class)
        ->call('edit', $project->id)
        ->set('image', UploadedFile::fake()->image('new.png', 700, 500))
        ->call('save')
        ->assertHasNoErrors();

    $project->refresh();

    expect($project->image_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($project->image_path);
});

it('keeps the current project image when a replacement upload is invalid', function () {
    Storage::fake('public');

    $oldPath = UploadedFile::fake()->image('old.jpg', 600, 400)->store('projects', 'public');
    $project = Project::factory()->create([
        'slug' => 'keep-image',
        'image_path' => $oldPath,
    ]);

    Livewire::test(ProjectsIndex::class)
        ->call('edit', $project->id)
        ->set('image', UploadedFile::fake()->image('bad.gif', 600, 400))
        ->call('save')
        ->assertHasErrors(['image']);

    expect($project->fresh()->image_path)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
});

it('rejects unsupported logo uploads in appearance settings', function () {
    Storage::fake('public');

    Livewire::test(AppearanceSettings::class)
        ->set('logo', UploadedFile::fake()->image('logo.gif', 300, 300))
        ->assertHasErrors(['logo']);
});
