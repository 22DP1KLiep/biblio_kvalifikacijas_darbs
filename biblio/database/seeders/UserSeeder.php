<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Anna Kalniņa',
                'username' => 'anna',
                'email' => 'anna@test.com',
                'password' => Hash::make('passsword'),
                'role' => 'user',
            ],
            [
                'name' => 'Jānis Bērziņš',
                'username' => 'janis',
                'email' => 'janis@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Laura Ozola',
                'username' => 'laura',
                'email' => 'laura@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Mārtiņš Liepa',
                'username' => 'martins',
                'email' => 'martins@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Elīna Krūmiņa',
                'username' => 'elina',
                'email' => 'elina@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // unikālais lauks
                $user
            );
        }
    }
}
