<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['game_date', 'location', 'opponent', 'team_score', 'opponent_score'];

    public function getResultAttribute(): ?string
    {
        if (is_null($this->team_score) || is_null($this->opponent_score)) {
            return null;
        }

        if ($this->team_score > $this->opponent_score) {
            return 'win';
        }

        if ($this->team_score < $this->opponent_score) {
            return 'loss';
        }

        return 'tie';
    }

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

    public function playerGameStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }
}
