<?php

namespace App\Http\Middleware;

use App\Models\RoleMenu;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $menuItems = [];
        $userPermissions = [];
        if ($request->user()) {
            $user = $request->user();

            // Get user permissions
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            // Get role IDs for menu filtering
            $roleIds = $user->roles->pluck('id');

            // Get menu items and check permissions
            $filteredMenus = RoleMenu::whereIn('role_id', $roleIds)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get()
                ->filter(function ($menu) use ($userPermissions) {
                    // Extract model name from href (e.g., /users -> users)
                    $model = str_replace('/', '', $menu->href);

                    // Check if user has view permission for this model
                    return in_array("view {$model}", $userPermissions);
                })
                ->map(function ($menu) {
                    return [
                        'title' => $menu->title,
                        'href' => $menu->href,
                        'icon' => $menu->icon,
                        'order' => $menu->order,
                    ];
                });

            // Remove duplicates based on title and href
            $uniqueMenus = collect($filteredMenus)->unique(function ($item) {
                return $item['title'] . '|' . $item['href'];
            })->sortBy('order')->values();

            $menuItems = $uniqueMenus->toArray();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'menuItems' => $menuItems,
            'userPermissions' => $userPermissions,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
