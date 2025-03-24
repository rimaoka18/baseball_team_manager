<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['game_id', 'runs'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
