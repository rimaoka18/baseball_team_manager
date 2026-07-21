<?php

namespace App\View\Composers;

use App\Services\TeamStatService;
use Illuminate\View\View;

class HeaderComposer
{
    protected $teamStatService;

    public function __construct(TeamStatService $teamStatService)
    {
        $this->teamStatService = $teamStatService;
    }

    public function compose(View $view): void
    {
        $record = $this->teamStatService->getTeamRecord();

        $view->with([
            'teamWins' => $record['wins'],
            'teamLosses' => $record['losses'],
            'teamWinRate' => $record['win_rate'],
            'nextGame' => $this->teamStatService->getNextUpcomingGame(),
        ]);
    }
}
