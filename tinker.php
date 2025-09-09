<?php
$user = App\Models\User::first();
$user->assignRole('admin');
