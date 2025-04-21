<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultUsers = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@paygo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@paygo.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Payroll Officer',
                'email' => 'payroll@paygo.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($defaultUsers as $user) {
            User::create($user);
        }
    }
}
