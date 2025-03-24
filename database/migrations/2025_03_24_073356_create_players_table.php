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
        Schema::create('players', function (Blueprint $table) {
            $table->id();                         // ID
            $table->string('name');              // 名前 (name)
            $table->string('position');          // ポジション (position: pitcher, catcher, etc.)
            $table->unsignedBigInteger('team_id'); // チームID (foreign key)
            $table->timestamps();                // created_at, updated_at

            // 外部キー制約（がいぶきーせいやく）
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
