<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->integer('coins')->default(0);
            $table->string('avatar')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->text('bio')->nullable();
            $table->integer('total_habits_completed')->default(0);
            $table->integer('longest_streak')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level', 'coins', 'avatar', 'is_admin', 'bio', 'total_habits_completed', 'longest_streak']);
        });
    }
};
