<?php

namespace App\Services;

use App\Models\Player;

class PlayerStatService
{
    public function getTopBattingAverages($limit = 3)
    {
        return Player::with('gameStats')
            ->get()
            ->map(fn($player) => [
                'player' => $player,
                'avg' => $this->getBattingAverageForPlayer($player) ?? 0,
            ])
            ->filter(fn($row) => $row['avg'] > 0)
            ->sortByDesc('avg')
            ->take($limit);
    }

    public function getTopERA($limit = 3)
    {
        return Player::with('gameStats')
            ->get()
            ->map(fn($player) => [
                'player' => $player,
                'era' => $this->getERAForPlayer($player),
            ])
            ->filter(fn($row) => $row['era'] !== null)
            ->sortBy('era')
            ->take($limit);
    }

    public function getAllPlayerStats()
    {
        return Player::with('gameStats')
            ->get()
            ->map(fn($player) => [
                'player' => $player,
                'at_bats' => $player->gameStats->sum('at_bats'),
                'hits' => $player->gameStats->sum('hits'),
                'avg' => $this->getBattingAverageForPlayer($player),
                'innings_pitched' => $player->gameStats->sum('innings_pitched'),
                'era' => $this->getERAForPlayer($player),
            ])
            // Include players who appeared in a game even with 0 AB / 0 IP.
            ->filter(fn($row) => $row['player']->gameStats->isNotEmpty())
            ->sortByDesc(fn($row) => $row['avg'] ?? -1)
            ->values();
    }

    public function getBattingAverageForPlayer(Player $player)
    {
        $totalAB = $player->gameStats->sum('at_bats');
        $totalHits = $player->gameStats->sum('hits');

        return $totalAB > 0 ? round($totalHits / $totalAB, 3) : null;
    }

    public function getERAForPlayer(Player $player)
    {
        $ip = $player->gameStats->sum('innings_pitched');
        $er = $player->gameStats->sum('earned_runs');

        return ($ip > 0 && $er !== null) ? round(($er * 9) / $ip, 2) : null;
    }
}
