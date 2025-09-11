<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RoleMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Testing\AssertableInertia as Assert;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cache permission agar tidak bentrok antar test
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions for users
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        Permission::firstOrCreate(['name' => 'view own users']);
        Permission::firstOrCreate(['name' => 'edit own users']);
        Permission::firstOrCreate(['name' => 'delete own users']);

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(['view users', 'create users', 'edit users', 'delete users']);

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo(['view users', 'edit users']);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo(['view own users', 'edit own users']);

        // Create users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');

        // Create menu items
        RoleMenu::firstOrCreate([
            'role_id' => $adminRole->id,
            'title'   => 'Users',
            'href'    => '/users',
            'icon'    => 'Users',
            'order'   => 1,
        ]);

        RoleMenu::firstOrCreate([
            'role_id' => $managerRole->id,
            'title'   => 'Users',
            'href'    => '/users',
            'icon'    => 'Users',
            'order'   => 1,
        ]);
    }

    /** @test */
    public function admin_can_view_users_index_page()
    {
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);
        $response->assertInertia(
            fn(Assert $page) =>
            $page->component('Users/Index')
                ->has('users')
                ->has('userPermissions')
                ->where('userPermissions', function ($permissions) {
                    return is_array($permissions) &&
                        in_array('view users', $permissions) &&
                        in_array('create users', $permissions) &&
                        in_array('edit users', $permissions) &&
                        in_array('delete users', $permissions);
                })
        );
    }

    /** @test */
    public function manager_can_view_users_index_page()
    {
        $response = $this->actingAs($this->manager)->get('/users');

        $response->assertStatus(200);
        $response->assertInertia(
            fn(Assert $page) =>
            $page->component('Users/Index')
                ->has('users')
                ->has('userPermissions')
                ->where('userPermissions', function ($permissions) {
                    return is_array($permissions) &&
                        in_array('view users', $permissions) &&
                        in_array('edit users', $permissions) &&
                        !in_array('create users', $permissions) &&
                        !in_array('delete users', $permissions);
                })
        );
    }

    /** @test */
    public function regular_user_can_only_view_own_data()
    {
        $response = $this->actingAs($this->regularUser)->get('/users');

        $response->assertStatus(200);
        $response->assertInertia(
            fn(Assert $page) =>
            $page->component('Users/Index')
                ->has('users')
                ->has('userPermissions')
                ->where('userPermissions', function ($permissions) {
                    return is_array($permissions) &&
                        in_array('view own users', $permissions) &&
                        in_array('edit own users', $permissions) &&
                        !in_array('view users', $permissions) &&
                        !in_array('create users', $permissions) &&
                        !in_array('delete users', $permissions);
                })
                ->where('users.data', function ($users) {
                    // Should only see their own user
                    return count($users) === 1 && $users[0]['id'] === $this->regularUser->id;
                })
        );
    }

    /** @test */
    public function guest_cannot_access_users_index_page()
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $newUserData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => [],
        ];

        $response = $this->actingAs($this->admin)->post('/users', $newUserData);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /** @test */
    public function manager_cannot_create_user()
    {
        $newUserData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->actingAs($this->manager)->post('/users', $newUserData);
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $targetUser = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $targetUser->email,
        ];

        $response = $this->actingAs($this->admin)->put("/users/{$targetUser->id}", $updateData);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function manager_can_update_user()
    {
        $targetUser = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $targetUser->email,
        ];

        $response = $this->actingAs($this->manager)->put("/users/{$targetUser->id}", $updateData);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function regular_user_can_only_update_own_data()
    {
        $otherUser = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $otherUser->email,
        ];

        // Try to update other user's data - should fail
        $response = $this->actingAs($this->regularUser)->put("/users/{$otherUser->id}", $updateData);
        $response->assertStatus(403);

        // Try to update own data - should succeed
        $ownUpdateData = [
            'name' => 'My Updated Name',
            'email' => $this->regularUser->email,
        ];

        $response = $this->actingAs($this->regularUser)->put("/users/{$this->regularUser->id}", $ownUpdateData);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'name' => 'My Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/users/{$targetUser->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    /** @test */
    public function manager_cannot_delete_user()
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->manager)->delete("/users/{$targetUser->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_delete_any_user()
    {
        $targetUser = User::factory()->create();

        // Try to delete other user - should fail
        $response = $this->actingAs($this->regularUser)->delete("/users/{$targetUser->id}");
        $response->assertStatus(403);

        // Try to delete own user - should fail (prevent self-deletion)
        $response = $this->actingAs($this->regularUser)->delete("/users/{$this->regularUser->id}");
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
        ]);
    }
}
