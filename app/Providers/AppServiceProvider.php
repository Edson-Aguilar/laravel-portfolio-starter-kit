<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\SystemSetting;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\SystemSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(SystemSetting::class, SystemSettingPolicy::class);
        Gate::guessPolicyNamesUsing(
            fn (string $modelClass) => str_replace('\\Models\\', '\\Policies\\', $modelClass).'Policy'
        );
        Gate::define('viewProjectsModule', fn () => config('starter.modules.projects') ? true : abort(404));
        Gate::define('viewAppearanceModule', fn () => config('starter.modules.appearance') ? true : abort(404));

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
