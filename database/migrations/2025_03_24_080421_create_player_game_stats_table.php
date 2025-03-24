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
        Schema::create('player_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');

            // Batting stats
            $table->unsignedTinyInteger('at_bats')->default(0);
            $table->unsignedTinyInteger('hits')->default(0);
            $table->unsignedTinyInteger('runs')->default(0);
            $table->unsignedTinyInteger('rbi')->default(0);
            $table->unsignedTinyInteger('walks')->default(0);
            $table->unsignedTinyInteger('strikeouts')->default(0);
            $table->unsignedTinyInteger('home_runs')->default(0);
            $table->unsignedTinyInteger('steals')->default(0);
            $table->unsignedTinyInteger('sacrifice_flies')->default(0);
            $table->unsignedTinyInteger('hbp')->default(0);

            // Pitching stats
            $table->decimal('innings_pitched', 4, 1)->nullable(); // e.g., 6.2 innings
            $table->unsignedTinyInteger('earned_runs')->nullable();
            $table->unsignedTinyInteger('pitching_strikeouts')->nullable();
            $table->unsignedTinyInteger('pitching_walks')->nullable();
            $table->unsignedTinyInteger('hits_allowed')->nullable();
            $table->unsignedSmallInteger('pitch_count')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_game_stats');
    }
};
