<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Update the enum to include 'admin'
            $table->enum('user_role', ['student', 'instructor', 'admin'])
                ->default('student')
                ->change();

            // Add a new status column (enabled/disabled)
            $table->enum('status', ['enabled', 'disabled'])
                ->default('enabled')
                ->after('profile_picture');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_role', ['student', 'instructor'])
                ->default('student')
                ->change();

            $table->dropColumn('status');
        });
    }
};
