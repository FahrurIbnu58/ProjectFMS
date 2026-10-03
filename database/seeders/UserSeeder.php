<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password'), 'role' => Role::Administrator->value]
        );
        User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Viewer', 'password' => Hash::make('password'), 'role' => Role::Viewer->value]
        );
    }
}
