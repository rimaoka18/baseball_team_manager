<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['game_date', 'game_time', 'location'];

    public function lineups()
    {
        return $this->hasMany(Lineup::class);
    }

    public function stats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }

    public function score()
    {
        return $this->hasOne(Score::class);
    }
}
