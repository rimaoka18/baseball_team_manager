<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Player extends Model
{
    protected $fillable = ['name', 'jersey_number', 'photo_path'];

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

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }
}
