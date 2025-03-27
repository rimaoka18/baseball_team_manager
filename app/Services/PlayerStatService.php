<?php

namespace App\Services;

use App\Models\Player;

class PlayerStatService
{
    public function getTopBattingAverages($limit = 3)
    {
        return Player::with('gameStats')
            ->get()
            ->map(function ($player) {
                $totalAB = $player->gameStats->sum('at_bats');
                $totalHits = $player->gameStats->sum('hits');
                $avg = $totalAB > 0 ? round($totalHits / $totalAB, 3) : 0;

                return [
                    'player' => $player,
                    'avg' => $avg,
                ];
            })
            ->filter(fn($row) => $row['avg'] > 0)
            ->sortByDesc('avg')
            ->take($limit);
    }

    public function getTopERA($limit = 3)
    {
        return Player::with('gameStats')
            ->get()
            ->map(function ($player) {
                $ip = $player->gameStats->sum('innings_pitched');
                $er = $player->gameStats->sum('earned_runs');
                $era = ($ip > 0 && $er !== null) ? round(($er * 9) / $ip, 2) : null;

                return [
                    'player' => $player,
                    'era' => $era,
                ];
            })
            ->filter(fn($row) => $row['era'] !== null)
            ->sortBy('era')
            ->take($limit);
    }
}
