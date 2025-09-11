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

                // Ambil role user
                $userRoles = $user->roles->pluck('id')->toArray();

                // Ambil menu berdasarkan role user
                $menuItems = RoleMenu::forRoles($userRoles)
                    ->topLevel()
                    ->with('children')
                    ->get()
                    ->map(function ($menuItem) {
                        return [
                            'title' => $menuItem->title,
                            'href'  => $menuItem->href,
                            'icon'  => $menuItem->icon,
                            'children' => $menuItem->children
                                ->map(function ($child) {
                                    return [
                                        'title' => $child->title,
                                        'href'  => $child->href,
                                        'icon'  => $child->icon,
                                    ];
                                })
                                ->values()
                                ->toArray(), // pastikan children jadi array murni
                        ];
                    })
                    ->values()
                    ->toArray(); // pastikan root menu juga array murni

                return [
                    'user'      => $user,
                    'menuItems' => $menuItems,
                ];
            },
        ]);
    }
}
