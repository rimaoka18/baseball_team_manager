<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name'];

    public function lineups()
    {
        return $this->hasMany(Lineup::class);
    }

    public function gameStats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }
}
