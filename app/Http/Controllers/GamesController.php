<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Services\PlayerStatService;

class GamesController extends Controller
{
    protected $game;
    protected $player;
    protected $playerGameStat;
    protected $playerStatService;

    public function __construct(Game $game, PlayerGameStat $playerGameStat, Player $player, PlayerStatService $playerStatService)
    {
        $this->game = $game;
        $this->playerGameStat = $playerGameStat;
        $this->player = $player;
        $this->playerStatService = $playerStatService;
    }

    public function index()
    {
        $topBatters = $this->playerStatService->getTopBattingAverages();
        $topPitchers = $this->playerStatService->getTopERA();
        $games = $this->game->orderBy('game_date', 'desc')->get();
        return view('games.index', compact('games', 'topBatters', 'topPitchers'));
    }

    public function show(Game $game)
    {
        $stats = $this->playerGameStat
            ->where('game_id', $game->id)
            ->get()
            ->map(function ($stat) {
                $stat->player = Player::find($stat->player_id); // 🔁 Force reload player
                return $stat;
            });

        $hitting = $stats;
        $pitching = $stats->filter(fn($s) => $s->innings_pitched !== null);

        return view('games.show', compact('game', 'hitting', 'pitching'));
    }


    public function create()
    {
        return view('games.create');
    }

    public function store(StoreGameRequest $request)
    {
        $this->game->fill($request->all())->save();
        $game = $this->game;

        foreach ($request->player_names as $index => $name) {
            if (empty(trim($name))) {
                continue;
            }

            $player = Player::create(['name' => $name]);

            $this->playerGameStat->fill([
                'game_id' => $game->id,
                'player_id' => $player->id,

                'at_bats' => $request->ab[$index] ?? 0,
                'runs' => $request->r[$index] ?? 0,
                'hits' => $request->h[$index] ?? 0,
                'rbi' => $request->rbi[$index] ?? 0,
                'home_runs' => $request->hr[$index] ?? 0,
                'walks' => $request->bb[$index] ?? 0,
                'strikeouts' => $request->k[$index] ?? 0,

                'innings_pitched' => $request->ip[$index]  ?? 0,
                'hits_allowed' => $request->ph[$index]  ?? 0,
                'earned_runs' => $request->er[$index]  ?? 0,
                'pitching_walks' => $request->pbb[$index]  ?? 0,
                'pitching_strikeouts' => $request->pk[$index]  ?? 0,

            ])->save();

            $this->player = new Player;
            $this->playerGameStat = new PlayerGameStat;
        }

        return redirect()->route('games.index')->with('success', 'ボックススコアが保存されました！');
    }

    public function edit(Game $game)
    {
        $stats = $game->playerGameStats()->with('player')->get();
        return view('games.edit', compact('game', 'stats'));
    }

    public function update(UpdateGameRequest $request, Game $game)
    {
        $game->update([
            'game_date' => $request->game_date,
            'location' => $request->location,
            'opponent' => $request->opponent,
            'team_score' => $request->team_score,
            'opponent_score' => $request->opponent_score,
        ]);

        foreach ($request->stat_ids as $index => $statId) {
            $stat = PlayerGameStat::with('player')->find($statId);

            if (!$stat || !$stat->player) continue;

            $stat->player->name = $request->player_names[$index];
            $stat->player->save();

            $stat->update([
                'at_bats' => $request->ab[$index] ?? 0,
                'runs' => $request->r[$index] ?? 0,
                'hits' => $request->h[$index] ?? 0,
                'rbi' => $request->rbi[$index] ?? 0,
                'home_runs' => $request->hr[$index] ?? 0,
                'walks' => $request->bb[$index] ?? 0,
                'strikeouts' => $request->k[$index] ?? 0,
                'innings_pitched' => $request->ip[$index] ?? null,
                'hits_allowed' => $request->ph[$index] ?? null,
                'earned_runs' => $request->er[$index] ?? null,
                'pitching_walks' => $request->pbb[$index] ?? null,
                'pitching_strikeouts' => $request->pk[$index] ?? null,
            ]);
        }

        return redirect()->route('games.index')->with('success', '試合情報を更新しました！');
    }

    public function destroy(Game $game)
    {
        $game->playerGameStats()->delete();

        $game->delete();

        return redirect()->route('games.index')->with('success', '試合を削除しました');
    }
}
