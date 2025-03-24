<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('teams', function (Blueprint $table) {
        $table->id();                    // ID
        $table->string('name');          // チーム名 (team name)
        $table->string('city')->nullable(); // 所属都市 (city), optional
        $table->timestamps();            // 作成日、更新日 (created_at, updated_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
