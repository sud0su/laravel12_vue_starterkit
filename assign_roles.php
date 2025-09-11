<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Assigning roles to users...\n";

// Get roles
$adminRole = Spatie\Permission\Models\Role::where('name', 'admin')->first();
$managerRole = Spatie\Permission\Models\Role::where('name', 'manager')->first();
$userRole = Spatie\Permission\Models\Role::where('name', 'user')->first();

// Get users
$adminUser = App\Models\User::where('email', 'admin@example.com')->first();
$managerUser = App\Models\User::where('email', 'manager@example.com')->first();
$regularUser = App\Models\User::where('email', 'user@example.com')->first();

// Assign roles
if ($adminUser && $adminRole) {
    $adminUser->assignRole($adminRole);
    echo "Assigned admin role to {$adminUser->name}\n";
}

if ($managerUser && $managerRole) {
    $managerUser->assignRole($managerRole);
    echo "Assigned manager role to {$managerUser->name}\n";
}

if ($regularUser && $userRole) {
    $regularUser->assignRole($userRole);
    echo "Assigned user role to {$regularUser->name}\n";
}

echo "\nUsers and their roles after assignment:\n";
echo "=======================================\n";

$users = App\Models\User::with('roles')->get();
foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->implode(', ');
    echo "{$user->name} ({$user->email}) - Roles: {$roles}\n";
}
