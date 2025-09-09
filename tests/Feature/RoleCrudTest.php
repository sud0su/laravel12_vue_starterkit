<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RoleAndPermissionSeeder;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

// CREATE TESTS
test('admin can create a new role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $roleData = [
        'name' => 'test-role',
        'guard_name' => 'web',
        'permissions' => [],
    ];

    actingAs($admin)
        ->post(route('roles.store'), $roleData)
        ->assertRedirect(route('roles.index'));

    $this->assertDatabaseHas('roles', [
        'name' => 'test-role',
        'guard_name' => 'web',
    ]);
});

test('role creation requires name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $roleData = [
        'guard_name' => 'web',
        'permissions' => [],
    ];

    actingAs($admin)
        ->post(route('roles.store'), $roleData)
        ->assertSessionHasErrors('name');
});

test('role name must be unique', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create first role
    Role::create(['name' => 'duplicate-role']);

    $roleData = [
        'name' => 'duplicate-role',
        'guard_name' => 'web',
        'permissions' => [],
    ];

    actingAs($admin)
        ->post(route('roles.store'), $roleData)
        ->assertSessionHasErrors('name');
});

test('permissions can be assigned to role during creation', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission1 = Permission::create(['name' => 'create-users']);
    $permission2 = Permission::create(['name' => 'edit-users']);

    $roleData = [
        'name' => 'manager',
        'guard_name' => 'web',
        'permissions' => [$permission1->id, $permission2->id],
    ];

    actingAs($admin)
        ->post(route('roles.store'), $roleData)
        ->assertRedirect(route('roles.index'));

    $role = Role::where('name', 'manager')->first();
    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions->pluck('name'))->toContain('create-users', 'edit-users');
});

// READ TESTS
test('admin can view role details', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'test-role']);

    actingAs($admin)
        ->get(route('roles.show', $role))
        ->assertStatus(200);
});

test('admin can access create role page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    actingAs($admin)->get(route('roles.create'))->assertStatus(200);
});

test('admin can access edit role page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'test-role']);

    actingAs($admin)
        ->get(route('roles.edit', $role))
        ->assertStatus(200);
});

// UPDATE TESTS
test('admin can update a role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'test-role']);

    $updateData = [
        'name' => 'updated-role',
        'guard_name' => 'web',
        'permissions' => [],
    ];

    actingAs($admin)
        ->put(route('roles.update', $role), $updateData)
        ->assertRedirect(route('roles.index'));

    $this->assertDatabaseHas('roles', [
        'name' => 'updated-role',
        'guard_name' => 'web',
    ]);
});

test('role update requires name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'test-role']);

    $updateData = [
        'guard_name' => 'web',
        'permissions' => [],
    ];

    actingAs($admin)
        ->put(route('roles.update', $role), $updateData)
        ->assertSessionHasErrors('name');
});

test('permissions can be updated on role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'manager']);
    $permission1 = Permission::create(['name' => 'create-users']);
    $permission2 = Permission::create(['name' => 'edit-users']);
    $permission3 = Permission::create(['name' => 'delete-users']);

    // Initially assign 2 permissions
    $role->syncPermissions([$permission1, $permission2]);

    $updateData = [
        'name' => 'manager',
        'guard_name' => 'web',
        'permissions' => [$permission2->id, $permission3->id], // Change permissions
    ];

    actingAs($admin)
        ->put(route('roles.update', $role), $updateData)
        ->assertRedirect(route('roles.index'));

    $role->refresh();
    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions->pluck('name'))->toContain('edit-users', 'delete-users');
});

test('permissions can be removed from role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'manager']);
    $permission = Permission::create(['name' => 'create-users']);
    $role->syncPermissions([$permission]);

    $updateData = [
        'name' => 'manager',
        'guard_name' => 'web',
        'permissions' => [], // Remove all permissions
    ];

    actingAs($admin)
        ->put(route('roles.update', $role), $updateData)
        ->assertRedirect(route('roles.index'));

    $role->refresh();
    expect($role->permissions)->toHaveCount(0);
});

// DELETE TESTS
test('admin can delete a role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'test-role']);

    actingAs($admin)
        ->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'));

    $this->assertDatabaseMissing('roles', [
        'name' => 'test-role',
    ]);
});

// ACCESS CONTROL TESTS
test('guests cannot access roles page', function () {
    get(route('roles.index'))->assertRedirect('login');
});

test('regular users cannot access roles page', function () {
    $user = User::factory()->create();
    actingAs($user)->get(route('roles.index'))->assertStatus(403);
});

test('admin can access roles page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    actingAs($admin)->get(route('roles.index'))->assertStatus(200);
});
