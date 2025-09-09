<?php

namespace Database\Seeders;

use App\Models\RoleMenu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $userRole = Role::where('name', 'user')->first();

        // Admin menu items
        if ($adminRole) {
            RoleMenu::create([
                'role_id' => $adminRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::create([
                'role_id' => $adminRole->id,
                'title' => 'Users',
                'href' => '/users',
                'icon' => 'Users',
                'order' => 2,
            ]);

            RoleMenu::create([
                'role_id' => $adminRole->id,
                'title' => 'Roles',
                'href' => '/roles',
                'icon' => 'Shield',
                'order' => 3,
            ]);

            RoleMenu::create([
                'role_id' => $adminRole->id,
                'title' => 'Settings',
                'href' => '/settings',
                'icon' => 'Settings',
                'order' => 4,
            ]);
        }

        // Manager menu items
        if ($managerRole) {
            RoleMenu::create([
                'role_id' => $managerRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::create([
                'role_id' => $managerRole->id,
                'title' => 'Users',
                'href' => '/users',
                'icon' => 'Users',
                'order' => 2,
            ]);

            RoleMenu::create([
                'role_id' => $managerRole->id,
                'title' => 'Reports',
                'href' => '/reports',
                'icon' => 'BarChart3',
                'order' => 3,
            ]);
        }

        // User menu items
        if ($userRole) {
            RoleMenu::create([
                'role_id' => $userRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::create([
                'role_id' => $userRole->id,
                'title' => 'Profile',
                'href' => '/profile',
                'icon' => 'User',
                'order' => 2,
            ]);
        }
    }
}
