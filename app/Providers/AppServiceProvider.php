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
        Inertia::share([
            'auth' => function () {
                $user = Auth::user();

                if (!$user) {
                    return null;
                }

                // Get user's roles
                $userRoles = $user->roles->pluck('id')->toArray();

                // Get menu items for user's roles
                $menuItems = RoleMenu::forRoles($userRoles)
                    ->topLevel()
                    ->with('children')
                    ->get()
                    ->map(function ($menuItem) {
                        return [
                            'title' => $menuItem->title,
                            'href' => $menuItem->href,
                            'icon' => $menuItem->icon,
                            'children' => $menuItem->children->map(function ($child) {
                                return [
                                    'title' => $child->title,
                                    'href' => $child->href,
                                    'icon' => $child->icon,
                                ];
                            }),
                        ];
                    });

                return [
                    'user' => $user,
                    'menuItems' => $menuItems,
                ];
            },
        ]);
    }
}
