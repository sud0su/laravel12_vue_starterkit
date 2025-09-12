<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar model yang akan dibuatkan permission secara otomatis
        $models = [
            'roles',
            'users',
            'soals', // Tambahkan model baru di sini
        ];

        // Generate permissions untuk setiap model
        foreach ($models as $model) {
            $this->createPermissionsForModel($model);
        }

        // Create global permissions that don't belong to specific models
        $globalPermissions = [
            'view dashboard',
            'manage settings',
            'access reports',
            'view analytics',
            'manage system',
            'access admin panel',
            'view logs',
            'manage backups',
        ];

        foreach ($globalPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo([
            // User management
            'view users',
            'create users',
            'edit users',
            'export users',
            'import users',

            // Role management
            'view roles',
            'manage roles',

            // Global permissions
            'view dashboard',
            'access reports',
            'view analytics',
            'access admin panel',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            // Basic user management
            'view users',
            'edit users',

            // Basic access
            'view dashboard',
        ]);
    }

    /**
     * Membuat permission yang lebih komprehensif untuk model tertentu
     */
    private function createPermissionsForModel(string $model): void
    {
        $permissions = [
            // Basic CRUD permissions
            "view {$model}",
            "create {$model}",
            "edit {$model}",
            "delete {$model}",

            // Advanced permissions
            "approve {$model}",
            "publish {$model}",
            "archive {$model}",
            "restore {$model}",
            "export {$model}",
            "import {$model}",

            // Management permissions
            "manage {$model}",
            "assign {$model}",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
