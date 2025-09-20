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
        // Instructor account
        User::create([
            'name' => 'Instructor One',
            'student_id' => null, // instructors don’t need student_id
            'email' => 'instructor@example.com',
            'password' => Hash::make('password123'),
            'user_role' => 'instructor',
            'profile_picture' => null, // optional
        ]);

        // Student account
        User::create([
            'name' => 'Student One',
            'student_id' => 'STU1001',
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
            'user_role' => 'student',
            'profile_picture' => null, // optional
        ]);
    }
}
