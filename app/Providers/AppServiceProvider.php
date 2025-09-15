<?php

namespace App\Providers;

use App\Models\RoleMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        // Logic for sharing data with Inertia has been moved to App\Http\Middleware\HandleInertiaRequests.php
        // This prevents conflicts and centralizes shared data management.
    }
}
