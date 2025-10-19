<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        // Create 5 instructor accounts
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Instructor ' . $i,
                'student_id' => null,
                'email' => 'instructor' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'user_role' => 'instructor',
                'profile_picture' => null,
            ]);
        }

        // Generate 30 student accounts
        for ($i = 1; $i <= 30; $i++) {
            User::create([
                'name' => 'Student ' . $i,
                'student_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'user_role' => 'student',
                'profile_picture' => null,
            ]);
        }
        */
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'student_id' => 'ADMIN001',
                'password' => Hash::make('password123'),
                'user_role' => 'admin',
                'status' => true,
            ]
        );
    }
}
