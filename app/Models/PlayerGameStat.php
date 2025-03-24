<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    protected $fillable = [
      'game_id', 'player_id',
      // Batting
      'at_bats', 'hits', 'runs', 'rbi', 'walks', 'strikeouts',
      'home_runs', 'steals', 'sacrifice_flies', 'hbp',
      // Pitching
      'innings_pitched', 'earned_runs', 'pitching_strikeouts',
      'pitching_walks', 'hits_allowed', 'pitch_count'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
