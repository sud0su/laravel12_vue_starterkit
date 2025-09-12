<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckResourcePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Get the route name (e.g., 'users.index', 'roles.create')
        $routeName = $request->route()->getName();

        if (!$routeName) {
            return $next($request);
        }

        // Extract resource name from route name (e.g., 'users' from 'users.index')
        $routeParts = explode('.', $routeName);
        $resource = $routeParts[0] ?? null;

        if (!$resource) {
            return $next($request);
        }

        // Determine the required permission based on the action
        $action = $routeParts[1] ?? 'index';
        $permission = $this->getRequiredPermission($resource, $action);

        // Check if user has the required permission
        if (!$user->hasPermissionTo($permission)) {
            return response()->json(['message' => 'User does not have the right permissions.'], 403);
        }

        return $next($request);
    }

    /**
     * Get the required permission based on resource and action
     */
    private function getRequiredPermission(string $resource, string $action): string
    {
        // Map actions to permission types
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        $permissionType = $actionMap[$action] ?? 'view';

        // Return permission like 'view users', 'create roles', etc.
        return $permissionType . ' ' . $resource;
    }
}
