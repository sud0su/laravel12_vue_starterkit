<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Database\Seeders\RoleAndPermissionSeeder;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

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
