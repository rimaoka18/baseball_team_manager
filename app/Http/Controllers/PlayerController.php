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
}
