<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Player;
use App\Services\PlayerStatService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function __construct(private PlayerStatService $playerStatService)
    {
    }

    public function roster()
    {
        $allPlayerStats = $this->playerStatService->getAllPlayerStats();

        return view('players.roster', compact('allPlayerStats'));
    }

    public function rankings()
    {
        $topBatters = $this->playerStatService->getTopBattingAverages();
        $topPitchers = $this->playerStatService->getTopERA();

        return view('players.roster-rankings', compact('topBatters', 'topPitchers'));
    }

    public function create()
    {
        return view('players.roster-add');
    }

    public function store(StorePlayerRequest $request)
    {
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('players', 'public')
            : null;

        Player::create([
            'name' => $request->name,
            'jersey_number' => $request->jersey_number,
            'photo_path' => $photoPath,
        ]);

        return redirect()
            ->route('roster.index')
            ->with('success', "「{$request->name}」を追加しました");
    }

    public function show(Player $player)
    {
        $player->load('gameStats');

        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $player->update([
            'name' => $request->name,
            'jersey_number' => $request->jersey_number,
        ]);

        return redirect()
            ->route('roster.players.show', $player)
            ->with('success', "「{$player->name}」を更新しました");
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $player = null;
        if ($query) {
            $player = Player::where('name', 'like', "%{$query}%")
                ->with('gameStats')
                ->first();
        }

        return view('players.search', compact('player', 'query'));
    }

    public function autocomplete(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $names = Player::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(8)
            ->pluck('name');

        return response()->json($names);
    }
}
