<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        if (!in_array('view users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        $users = User::with('roles')->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => Role::all(),
            'userPermissions' => $userPermissions,
        ]);
    }

    public function create(): Response
    {
        $user = auth()->user();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        if (!in_array('create users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Users/Create', [
            'allRoles' => Role::all()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $userAuth = auth()->user();
        $userPermissions = $userAuth->getAllPermissions()->pluck('name')->toArray();

        if (!in_array('create users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $userAuth = auth()->user();
        $userPermissions = $userAuth->getAllPermissions()->pluck('name')->toArray();

        // If user has only edit own permission, check ownership
        if (!in_array('edit users', $userPermissions) && in_array('edit own users', $userPermissions)) {
            if ($user->id !== $userAuth->id) {
                abort(403, 'Unauthorized');
            }
        } elseif (!in_array('edit users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
            'allRoles' => Role::all(),
            'userRoles' => $user->roles->pluck('id')->toArray()
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $userAuth = auth()->user();
        $userPermissions = $userAuth->getAllPermissions()->pluck('name')->toArray();

        // If user has only edit own permission, check ownership
        if (!in_array('edit users', $userPermissions) && in_array('edit own users', $userPermissions)) {
            if ($user->id !== $userAuth->id) {
                abort(403, 'Unauthorized');
            }
        } elseif (!in_array('edit users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->getKey())],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ];

        // Add password validation only if password is provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $userAuth = auth()->user();
        $userPermissions = $userAuth->getAllPermissions()->pluck('name')->toArray();

        // Prevent deleting the current authenticated user
        if ($user->getKey() === $userAuth->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // If user has only delete own permission, check ownership
        if (!in_array('delete users', $userPermissions) && in_array('delete own users', $userPermissions)) {
            if ($user->id !== $userAuth->id) {
                abort(403, 'Unauthorized');
            }
        } elseif (!in_array('delete users', $userPermissions)) {
            abort(403, 'Unauthorized');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
