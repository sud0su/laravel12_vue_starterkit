<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions')->paginate(10);

        // Add permission counts grouped by model for each role
        $roles->getCollection()->transform(function ($role) {
            $role->permission_counts = $role->permissions->map(function ($permission) {
                $parts = explode(' ', $permission->name);
                $model = 'other';

                if (count($parts) >= 2) {
                    if ($parts[1] === 'own') {
                        $model = $parts[2] ?? 'other';
                    } else {
                        $model = $parts[1];
                    }
                }

                $permission->model = $model;
                $permission->action = $parts[0] . (isset($parts[1]) && $parts[1] === 'own' ? ' own' : '');

                return $permission;
            })->groupBy('model')->map(function ($permissions, $model) {
                return [
                    'model' => $model,
                    'count' => $permissions->count(),
                    'permissions' => $permissions->pluck('name')->toArray()
                ];
            })->values();

            return $role;
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'permissions' => $this->groupPermissionsByModel()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
            'guard_name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role): Response
    {
        return Inertia::render('Roles/Show', [
            'role' => $role->load('permissions')
        ]);
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->groupPermissionsByModel()
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'guard_name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    private function groupPermissionsByModel()
    {
        return Permission::all()->map(function ($permission) {
            $parts = explode(' ', $permission->name);
            $model = 'other';

            if (count($parts) >= 2) {
                if ($parts[1] === 'own') {
                    $model = $parts[2] ?? 'other';
                } else {
                    $model = $parts[1];
                }
            }

            $permission->model = $model;
            $permission->action = $parts[0] . (isset($parts[1]) && $parts[1] === 'own' ? ' own' : '');

            return $permission;
        })->groupBy('model');
    }
}
