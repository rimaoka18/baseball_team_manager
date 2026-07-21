<?php

namespace App\Services;

use App\Models\Game;

class TeamStatService
{
    public function getTeamRecord()
    {
        $games = Game::whereNotNull('team_score')->whereNotNull('opponent_score')->get();

        $wins = $games->filter(fn ($game) => $game->result === 'win')->count();
        $losses = $games->filter(fn ($game) => $game->result === 'loss')->count();

        $winRate = ($wins + $losses) > 0 ? round($wins / ($wins + $losses), 3) : null;

        return [
            'wins' => $wins,
            'losses' => $losses,
            'win_rate' => $winRate,
        ];
    }

    public function getNextUpcomingGame()
    {
        return Game::whereNull('team_score')
            ->whereDate('game_date', '>=', now()->toDateString())
            ->orderBy('game_date')
            ->first();
    }
}
