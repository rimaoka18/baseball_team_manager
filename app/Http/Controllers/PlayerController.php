<?php

namespace App\Http\Controllers;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
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
