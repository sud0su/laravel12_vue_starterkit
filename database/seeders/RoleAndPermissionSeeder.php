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

        // create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
    }

    /**
     * Membuat permission CRUD untuk model tertentu
     */
    private function createPermissionsForModel(string $model): void
    {
        $permissions = [
            "view {$model}",
            "create {$model}",
            "edit {$model}",
            "delete {$model}",
            "view own {$model}",
            "edit own {$model}",
            "delete own {$model}",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
