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
        Schema::table('player_game_stats', function (Blueprint $table) {
            $table->unsignedTinyInteger('batting_order')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_game_stats', function (Blueprint $table) {
            $table->dropColumn('batting_order');
        });
    }
};
