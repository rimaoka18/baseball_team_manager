<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'at_bats',
        'runs',
        'hits',
        'rbi',
        'home_runs',
        'walks',
        'strikeouts',
        'innings_pitched',
        'hits_allowed',
        'earned_runs',
        'pitching_walks',
        'pitching_strikeouts',
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
