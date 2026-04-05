<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@imamku.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@imamku.test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => null,
                'is_active' => true,
            ]
        );
    }
}
