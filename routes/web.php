<?php

use App\Livewire\Auth\Login;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'projects' => Project::where('status', 'published')->latest('published_at')->take(6)->get(),
    ]);
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::get('/idioma/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['es', 'en'], true), 404);

    session(['locale' => $locale]);

    return back();
})->middleware('web')->name('locale.switch');

Route::view('/dashboard', 'admin.dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::view('/admin/users', 'admin.users')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.users');

Route::view('/admin/projects', 'admin.projects')
    ->middleware(['auth', 'role:admin|editor'])
    ->name('admin.projects');

Route::view('/admin/appearance', 'admin.appearance')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.appearance');
