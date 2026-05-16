<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['casual', 'competitive', 'ladder'])->default('casual');
            // Team A
            $table->foreignId('team_a_p1')->constrained('players')->cascadeOnDelete();
            $table->foreignId('team_a_p2')->constrained('players')->cascadeOnDelete();
            // Team B
            $table->foreignId('team_b_p1')->constrained('players')->cascadeOnDelete();
            $table->foreignId('team_b_p2')->constrained('players')->cascadeOnDelete();
            // Scores
            $table->unsignedTinyInteger('score_a')->nullable();
            $table->unsignedTinyInteger('score_b')->nullable();
            // Winner: 'A', 'B', or null (pending)
            $table->enum('winner', ['A', 'B'])->nullable();
            $table->boolean('ratings_applied')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
