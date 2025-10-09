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
        /*
        User::create([
            'name' => 'Instructor One',
            'student_id' => null,
            'email' => 'instructor@example.com',
            'password' => Hash::make('password123'),
            'user_role' => 'instructor',
            'profile_picture' => null,
        ]);
        */
        
        // Generate 20 student accounts
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => 'Student ' . $i,
                'student_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'user_role' => 'student',
                'profile_picture' => null,
            ]);
        }
    }
}
