<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
 $table->id();

    $table->foreignId('student_id')
        ->constrained()
        ->cascadeOnDelete();

        $table->foreignId('game_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId('skill_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('status', [
        'not_started',
        'failed',
        'completed'
    ])->default('not_started');

    $table->integer('score')->default(0);
    $table->integer('mistakes')->default(0);
    $table->json('hints_used');
    $table->integer('attempts_count')->default(0);

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
