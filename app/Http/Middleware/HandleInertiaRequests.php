<?php

namespace App\Http\Middleware;

use App\Models\RoleMenu;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'name' => config('app.name'),
            'quote' => fn () => ['message' => trim(str($this->getQuote())->before('-')->__toString())],
            'auth' => [
                'user' => $user,
            ],
            'menuItems' => $user ? $this->buildUserMenu($user) : [],
            'userPermissions' => $user ? $user->getAllPermissions()->pluck('name')->toArray() : [],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ]);
    }

    private function getQuote(): string
    {
        return Inspiring::quotes()->random();
    }

    private function buildUserMenu(User $user): array
    {
        $menuMap = config('menu.map');

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $menuItems = collect();

        foreach ($menuMap as $permission => $menuData) {
            if (in_array($permission, $userPermissions)) {
                $menuItems->push($menuData);
            }
        }

        // Urutkan berdasarkan order dan konversi ke array
        return $menuItems->sortBy('order')->values()->toArray();
    }
}
