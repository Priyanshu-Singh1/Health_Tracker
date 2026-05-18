<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('xp_reward')->default(10);
            $table->integer('coin_reward')->default(5);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->integer('total_completions')->default(0);
            $table->date('last_completed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->time('reminder_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
