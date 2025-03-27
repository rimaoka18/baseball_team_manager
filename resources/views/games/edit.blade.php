@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">✏️ 試合編集</h1>

<form action="{{ route('games.update', $game) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">試合日</label>
            <input type="date" name="game_date" value="{{ $game->game_date }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">場所</label>
            <input type="text" name="location" value="{{ $game->location }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">相手チーム名</label>
            <input type="text" name="opponent" value="{{ $game->opponent }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm font-medium">自チーム得点</label>
                <input type="number" name="team_score" value="{{ $game->team_score }}" required class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">相手得点</label>
                <input type="number" name="opponent_score" value="{{ $game->opponent_score }}" required class="mt-1 w-full border rounded px-3 py-2">
            </div>
        </div>
    </div>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-2">選手成績</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">選手名</th>
                    <th class="px-2 py-1 border">AB</th>
                    <th class="px-2 py-1 border">R</th>
                    <th class="px-2 py-1 border">H</th>
                    <th class="px-2 py-1 border">RBI</th>
                    <th class="px-2 py-1 border">HR</th>
                    <th class="px-2 py-1 border">BB</th>
                    <th class="px-2 py-1 border">K</th>
                    <th class="px-2 py-1 border">IP</th>
                    <th class="px-2 py-1 border">H(P)</th>
                    <th class="px-2 py-1 border">R(P)</th>
                    <th class="px-2 py-1 border">ER</th>
                    <th class="px-2 py-1 border">BB(P)</th>
                    <th class="px-2 py-1 border">K(P)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stats as $stat)
                    <tr>
                        <td class="border px-2 py-1">
                            <input type="text" name="player_names[]" value="{{ $stat->player->name }}"
                                   class="w-32 border rounded px-2 py-1" required>
                            <input type="hidden" name="stat_ids[]" value="{{ $stat->id }}">
                        </td>
                        <td class="border px-2 py-1"><input type="number" name="ab[]" value="{{ $stat->at_bats }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="r[]" value="{{ $stat->runs }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="h[]" value="{{ $stat->hits }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="rbi[]" value="{{ $stat->rbi }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="hr[]" value="{{ $stat->home_runs }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="bb[]" value="{{ $stat->walks }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="k[]" value="{{ $stat->strikeouts }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" step="0.1" name="ip[]" value="{{ $stat->innings_pitched }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="ph[]" value="{{ $stat->hits_allowed }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="pr[]" value="{{ $stat->pr }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="er[]" value="{{ $stat->earned_runs }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="pbb[]" value="{{ $stat->pitching_walks }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                        <td class="border px-2 py-1"><input type="number" name="pk[]" value="{{ $stat->pitching_strikeouts }}" class="w-12 px-1 py-1 border rounded text-center"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
            更新する
        </button>
    </div>
</form>
@endsection
