<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin 
                            {--name= : The name of the superadmin user} 
                            {--email= : The email of the superadmin user} 
                            {--password= : The password for the superadmin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a superadmin user with all permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating SuperAdmin User...');

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter superadmin name', 'Super Admin');
        $email = $this->option('email') ?: $this->ask('Enter superadmin email', 'admin@example.com');
        $password = $this->option('password') ?: $this->secret('Enter superadmin password (min 8 characters)');

        // Validate password length
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return 1;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create or get superadmin role
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Get all permissions
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->warn('No permissions found. Please run the seeders first:');
            $this->line('php artisan db:seed --class=RoleAndPermissionSeeder');
            return 1;
        }

        // Assign all permissions to superadmin role
        $superAdminRole->syncPermissions($permissions);

        // Create the superadmin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
        $user->save();

        // Assign superadmin role to user
        $user->assignRole($superAdminRole);

        $this->info('SuperAdmin user created successfully!');
        $this->line("Name: {$name}");
        $this->line("Email: {$email}");
        $this->line("Role: superadmin");
        $this->line("Permissions: All permissions assigned");

        $this->newLine();
        $this->comment('You can now login with these credentials.');

        return 0;
    }
}
