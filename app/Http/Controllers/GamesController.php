<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Lineup;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\StoreUpcomingGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateUpcomingGameRequest;
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
        $upcomingGames = $this->game->whereNull('team_score')
            ->orderBy('game_date', 'asc')
            ->get();

        $completedGames = $this->game->whereNotNull('team_score')
            ->orderBy('game_date', 'desc')
            ->get();

        return view('games.index', compact('upcomingGames', 'completedGames'));
    }

    public function upcoming()
    {
        $upcomingGames = $this->game->whereNull('team_score')
            ->whereDate('game_date', '>=', now()->toDateString())
            ->orderBy('game_date')
            ->with(['lineups' => fn($query) => $query->with('player.gameStats')->orderBy('batting_order')])
            ->get();

        $playerStatService = $this->playerStatService;

        return view('games.upcoming', compact('upcomingGames', 'playerStatService'));
    }

    public function stats()
    {
        return redirect()->route('roster.index');
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

        $lineups = $stats->isEmpty()
            ? $game->lineups()->with('player')->orderBy('batting_order')->get()
            : collect();

        return view('games.show', compact('game', 'hitting', 'pitching', 'lineups'));
    }


    public function create()
    {
        $previousGame = $this->previousLineupGame(null, now()->toDateString());

        return view('games.create', compact('previousGame'));
    }

    private function previousLineupGame(?int $excludingGameId = null, ?string $beforeDate = null): ?Game
    {
        return $this->game
            ->whereHas('lineups')
            ->when($excludingGameId, fn ($q) => $q->where('id', '!=', $excludingGameId))
            ->when($beforeDate, fn ($q) => $q->where('game_date', '<', $beforeDate))
            ->with(['lineups' => fn ($q) => $q->with('player')->orderBy('batting_order')])
            ->orderByDesc('game_date')
            ->orderByDesc('id')
            ->first();
    }

    public function store(StoreGameRequest $request)
    {
        $this->game->fill($request->all())->save();
        $game = $this->game;

        foreach ($request->player_names as $index => $name) {
            if (empty(trim($name))) {
                continue;
            }

            $player = Player::firstOrCreate(['name' => $name]);

            $this->playerGameStat->fill([
                'game_id' => $game->id,
                'player_id' => $player->id,
                'position' => $request->position[$index] ?? null,

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

        return redirect()->route('games.index');
    }

    public function createUpcoming()
    {
        $previousGame = $this->previousLineupGame(null, now()->toDateString());
        $players = Player::orderBy('name')->get();

        return view('games.upcoming-create', compact('previousGame', 'players'));
    }

    public function storeUpcoming(StoreUpcomingGameRequest $request)
    {
        $game = Game::create([
            'game_date' => $request->game_date,
            'game_time' => $request->game_time,
            'location' => $request->location,
            'opponent' => $request->opponent,
        ]);

        foreach ($request->player_ids as $index => $playerId) {
            if (empty($playerId)) {
                continue;
            }

            Lineup::create([
                'game_id' => $game->id,
                'player_id' => $playerId,
                'batting_order' => $index + 1,
                'position' => $request->position[$index] ?? '',
            ]);
        }

        return redirect()->route('games.upcoming.index')->with('success', '試合予定を登録しました！');
    }

    public function editUpcoming(Game $game)
    {
        $lineups = $game->lineups()->with('player')->orderBy('batting_order')->get();
        $previousGame = $this->previousLineupGame($game->id, $game->game_date);

        return view('games.upcoming-edit', compact('game', 'lineups', 'previousGame'));
    }

    public function updateUpcoming(UpdateUpcomingGameRequest $request, Game $game)
    {
        $game->update([
            'game_date' => $request->game_date,
            'game_time' => $request->game_time,
            'location' => $request->location,
            'opponent' => $request->opponent,
        ]);

        $keptLineupIds = [];

        foreach ($request->player_names as $index => $name) {
            $name = trim($name);
            $lineupId = ($request->lineup_ids ?? [])[$index] ?? null;

            if ($name === '') {
                continue;
            }

            $player = Player::firstOrCreate(['name' => $name]);

            $lineup = $lineupId ? Lineup::find($lineupId) : null;

            if ($lineup) {
                $lineup->update([
                    'player_id' => $player->id,
                    'batting_order' => $index + 1,
                    'position' => $request->position[$index] ?? '',
                ]);
            } else {
                $lineup = Lineup::create([
                    'game_id' => $game->id,
                    'player_id' => $player->id,
                    'batting_order' => $index + 1,
                    'position' => $request->position[$index] ?? '',
                ]);
            }

            $keptLineupIds[] = $lineup->id;
        }

        // Any existing lineup rows whose name was cleared out get removed.
        $game->lineups()->whereNotIn('id', $keptLineupIds)->delete();

        $redirectRoute = $request->input('from') === 'games.index' ? 'games.index' : 'games.upcoming.index';

        return redirect()->route($redirectRoute)->with('success', '試合予定を更新しました！');
    }

    public function edit(Game $game)
    {
        $stats = $game->playerGameStats()->with('player')->get();
        $rowsAreBlankPlaceholders = false;

        if ($stats->isEmpty()) {
            $lineups = $game->lineups()->with('player')->orderBy('batting_order')->get();

            if ($lineups->isNotEmpty()) {
                // No box score entered yet — seed the edit form from the lineup.
                $stats = $lineups->map(function ($lineup) {
                    $stat = new PlayerGameStat([
                        'player_id' => $lineup->player_id,
                        'position' => $lineup->position,
                    ]);
                    $stat->setRelation('player', $lineup->player);
                    $stat->lineup_id = $lineup->id;

                    return $stat;
                });
            } else {
                // Upcoming game with no lineup yet — show empty rows so the user
                // can enter names, positions, and box-score stats.
                $rowsAreBlankPlaceholders = true;
                $stats = collect(range(1, 9))->map(function () {
                    $stat = new PlayerGameStat();
                    $stat->setRelation('player', new Player(['name' => '']));
                    $stat->lineup_id = null;

                    return $stat;
                });
            }
        } else {
            $lineupsByPlayerId = $game->lineups()->get()->keyBy('player_id');

            $stats->each(function ($stat) use ($lineupsByPlayerId) {
                $lineup = $lineupsByPlayerId->get($stat->player_id);
                if ($lineup) {
                    $stat->lineup_id = $lineup->id;
                    if (empty($stat->position)) {
                        $stat->position = $lineup->position;
                    }
                }
            });
        }

        // Overwriting player_names[] on a row that already has a stat_id/lineup_id
        // renames the existing linked Player in place (see update()) rather than
        // reassigning to a different player — safe for fixing a typo, not for
        // bulk-swapping in a different roster. Only offer the previous-lineup
        // template when every row is still an unlinked blank placeholder.
        $previousGame = $rowsAreBlankPlaceholders ? $this->previousLineupGame($game->id, $game->game_date) : null;

        return view('games.edit', compact('game', 'stats', 'previousGame'));
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

        $isCompleted = $request->team_score !== null && $request->opponent_score !== null;

        foreach ($request->stat_ids as $index => $statId) {
            $name = trim($request->player_names[$index] ?? '');
            $lineupId = ($request->lineup_ids ?? [])[$index] ?? null;

            if ($name === '') {
                continue;
            }

            if ($statId) {
                $stat = PlayerGameStat::with('player')->find($statId);

                if (!$stat || !$stat->player) continue;

                $stat->player->name = $name;
                $stat->player->save();
            } elseif ($lineupId && !$isCompleted) {
                // Still upcoming (no score entered yet) — just keep the lineup's
                // player in sync, no box score exists to create yet.
                $lineup = Lineup::with('player')->find($lineupId);

                if ($lineup && $lineup->player) {
                    $lineup->player->name = $name;
                    $lineup->player->save();
                    $lineup->update([
                        'batting_order' => $index + 1,
                        'position' => $request->position[$index] ?? '',
                    ]);
                }

                continue;
            } else {
                $player = Player::firstOrCreate(['name' => $name]);
                $stat = new PlayerGameStat([
                    'game_id' => $game->id,
                    'player_id' => $player->id,
                ]);
            }

            $stat->fill([
                'position' => $request->position[$index] ?? null,
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
            ])->save();

            if ($lineupId) {
                $lineup = Lineup::find($lineupId);
                if ($lineup) {
                    $lineup->update([
                        'batting_order' => $index + 1,
                        'position' => $request->position[$index] ?? '',
                        'player_id' => $stat->player_id,
                    ]);
                }
            }
        }

        if (is_null($game->team_score) || is_null($game->opponent_score)) {
            return redirect()->route('games.upcoming.index')->with('success', '試合情報を更新しました！');
        }

        return redirect()->route('games.show', $game)->with('success', '試合情報を更新しました！');
    }

    public function destroy(Game $game)
    {
        $wasUpcoming = is_null($game->team_score) || is_null($game->opponent_score);

        $game->playerGameStats()->delete();

        $game->delete();

        if ($wasUpcoming) {
            return redirect()->route('games.upcoming.index')->with('success', '試合を削除しました');
        }

        return redirect()->route('games.index')->with('success', '試合を削除しました');
    }
}
