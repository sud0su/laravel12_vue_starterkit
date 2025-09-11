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
        $superAdminRole = Role::where('name', 'superadmin')->first();

        if ($adminRole) {
            RoleMenu::firstOrCreate([
                'role_id' => $adminRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $adminRole->id,
                'title' => 'Users',
                'href' => '/users',
                'icon' => 'Users',
                'order' => 2,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $adminRole->id,
                'title' => 'Roles',
                'href' => '/roles',
                'icon' => 'Shield',
                'order' => 3,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $adminRole->id,
                'title' => 'Permissions',
                'href' => '/permissions',
                'icon' => 'Lock',
                'order' => 4,
            ]);
        }

        if ($managerRole) {
            RoleMenu::firstOrCreate([
                'role_id' => $managerRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $managerRole->id,
                'title' => 'Users',
                'href' => '/users',
                'icon' => 'Users',
                'order' => 2,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $managerRole->id,
                'title' => 'Reports',
                'href' => '/reports',
                'icon' => 'BarChart3',
                'order' => 3,
            ]);
        }

        if ($userRole) {
            RoleMenu::firstOrCreate([
                'role_id' => $userRole->id,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'order' => 1,
            ]);

            RoleMenu::firstOrCreate([
                'role_id' => $userRole->id,
                'title' => 'Profile',
                'href' => '/profile',
                'icon' => 'User',
                'order' => 2,
            ]);
        }

        if ($superAdminRole) {
            // Assign all menu items to the superadmin role
            if ($adminRole) {
                $adminRoleMenus = $adminRole->roleMenus()->get();
                foreach ($adminRoleMenus as $menu) {
                    RoleMenu::firstOrCreate([
                        'role_id' => $superAdminRole->id,
                        'title' => $menu->title,
                        'href' => $menu->href,
                        'icon' => $menu->icon,
                        'order' => $menu->order,
                    ]);
                }
            }
        }
    }
}
