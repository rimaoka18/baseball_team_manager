<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MAP = [
        'P' => '投',
        'C' => '捕',
        '1B' => '一',
        '2B' => '二',
        '3B' => '三',
        'SS' => '遊',
        'LF' => '左',
        'CF' => '中',
        'RF' => '右',
        'DH' => '指',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::MAP as $english => $kanji) {
            DB::table('lineups')->where('position', $english)->update(['position' => $kanji]);
            DB::table('player_game_stats')->where('position', $english)->update(['position' => $kanji]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::MAP as $english => $kanji) {
            DB::table('lineups')->where('position', $kanji)->update(['position' => $english]);
            DB::table('player_game_stats')->where('position', $kanji)->update(['position' => $english]);
        }
    }
};
