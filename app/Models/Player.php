<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'jersey_number'];

    public function lineups()
    {
        return $this->hasMany(Lineup::class);
    }

    public function gameStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }

    public function rosterLabel(): string
    {
        if ($this->jersey_number === null) {
            return $this->name;
        }

        return "#{$this->jersey_number} {$this->name}";
    }
}
