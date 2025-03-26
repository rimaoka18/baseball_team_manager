<?php

namespace Database\Seeders;
use App\Models\Player;
use App\Models\Game;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseballSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
      {
          $players = [
            '今岡 稜',
            '佐藤 次郎',
            '鈴木 花子',
            '高橋 拓海',
            '山本 結衣',
            '中村 健太',
            '加藤 翼',
            '伊藤 美咲',
            '小林 陽菜',
            '渡辺 涼',
          ];
          foreach ($players as $name) {
            Player::create(['name' => $name]);
          }
            // 試合を作成（今日の日付）
          Game::create([
              'game_date' => now()->format('Y-m-d'),
              'game_time' => '14:00',
              'location' => '二子玉川緑地運動場',
          ]);
      }
}
