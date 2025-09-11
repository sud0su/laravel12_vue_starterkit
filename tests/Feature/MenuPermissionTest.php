<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RoleMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeders to ensure consistent data
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleMenuSeeder::class);

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        // Verify permissions are assigned
        $this->assertTrue($this->admin->hasPermissionTo('view users'));
        $this->assertTrue($this->user->hasPermissionTo('view own users'));
    }

    public function test_admin_sees_users_menu_based_on_permissions()
    {
        // Test the /users route which is protected by auth middleware
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);

        $response->assertInertia(
            fn($page) => $page
                ->has('menuItems')
                ->where('menuItems', function ($menuItems) {
                    $usersMenu = collect($menuItems)->firstWhere('href', '/users');
                    $dashboardMenu = collect($menuItems)->firstWhere('href', '/dashboard');
                    return $usersMenu && $dashboardMenu;
                })
        );
    }

    public function test_regular_user_does_not_see_users_menu()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertInertia(
            fn($page) => $page
                ->has('menuItems')
                ->where('menuItems', function ($menuItems) {
                    $usersMenu = collect($menuItems)->firstWhere('href', '/users');
                    $dashboardMenu = collect($menuItems)->firstWhere('href', '/dashboard');
                    return !$usersMenu && $dashboardMenu;
                })
        );
    }
}
