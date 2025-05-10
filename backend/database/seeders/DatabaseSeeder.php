<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Super Admin
        User::updateOrCreate([
            'email' => 'super@admin.com',
        ], [
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password@admin'),
            'role' => RolesEnum::SUPER_ADMIN->value,
        ]);
    }
}
