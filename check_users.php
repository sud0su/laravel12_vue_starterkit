<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Users and their roles:\n";
echo "=====================\n";

$users = App\Models\User::with('roles')->get();

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->implode(', ');
    echo "{$user->name} ({$user->email}) - Roles: {$roles}\n";
}

echo "\nAvailable roles:\n";
echo "================\n";

$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "{$role->name}\n";
}
