@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<h1 class="text-2xl font-bold mb-6">試合編集</h1>

<form action="{{ route('games.update', $game) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-bf-navy mb-4">試合情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-bf-navy">
            <div class="min-w-0">
                <label class="block text-sm font-medium">試合日</label>
                <input type="date" name="game_date" value="{{ $game->game_date }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-medium">場所</label>
                <input type="text" name="location" value="{{ $game->location }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-medium">相手チーム名</label>
                <input type="text" name="opponent" value="{{ $game->opponent }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-2 min-w-0">
                <div class="min-w-0">
                    <label class="block text-sm font-medium">自チーム得点</label>
                    <input type="number" name="team_score" value="{{ $game->team_score }}" class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
                </div>
                <div class="min-w-0">
                    <label class="block text-sm font-medium">相手得点</label>
                    <input type="number" name="opponent_score" value="{{ $game->opponent_score }}" class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
                </div>
            </div>
        </div>
    </div>

    @php
        $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
    @endphp

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-bf-navy">選手成績</h2>
            @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full text-sm bg-bf-cream">
                <thead class="bg-bf-navy text-white">
                    <tr>
                        <th class="px-2 py-1 border w-8"></th>
                        <th class="px-2 py-1 border">打順</th>
                        <th class="px-2 py-1 border">選手名</th>
                        <th class="px-2 py-1 border">守備</th>
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
                <tbody id="stat-rows" class="text-gray-800">
                    @foreach ($stats as $index => $stat)
                        @php $playerName = $stat->player->name ?? ''; @endphp
                        <tr>
                            <td class="border px-2 py-1 text-center drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none select-none">
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="9" cy="6" r="1.5" /><circle cx="15" cy="6" r="1.5" />
                                    <circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" />
                                    <circle cx="9" cy="18" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                                </svg>
                            </td>
                            <td class="border px-2 py-1 text-center font-semibold batting-order">{{ $stat->batting_order ?? ($index + 1) }}</td>
                            <td class="border px-2 py-1 player-name-cell">
                                @if ($playerName !== '')
                                    <span class="player-name-label font-medium whitespace-nowrap">{{ $playerName }}</span>
                                    <input type="hidden" name="player_names[]" class="player-name-input" value="{{ $playerName }}">
                                @else
                                    <select name="player_names[]" class="player-name-input w-36 px-1 py-1 border rounded">
                                        <option value="">-</option>
                                        @foreach ($players as $player)
                                            <option value="{{ $player->name }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <input type="hidden" name="stat_ids[]" value="{{ $stat->id }}">
                                <input type="hidden" name="lineup_ids[]" value="{{ $stat->lineup_id }}">
                            </td>
                            <td class="border px-2 py-1">
                                <select name="position[]" class="w-20 px-1 py-1 border rounded">
                                    <option value="">-</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position }}" @selected(($stat->position ?? '') === $position)>{{ $position }}</option>
                                    @endforeach
                                </select>
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

        <div class="mt-4">
            <button type="button" id="add-stat-row-btn" onclick="addPlayerStatRow()" class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-navy-light transition">＋選手を追加</button>
        </div>
    </div>

    <div>
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition">
            更新する
        </button>
    </div>
</form>

<template id="empty-stat-row-template">
    <tr>
        <td class="border px-2 py-1 text-center drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none select-none">
            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="9" cy="6" r="1.5" /><circle cx="15" cy="6" r="1.5" />
                <circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" />
                <circle cx="9" cy="18" r="1.5" /><circle cx="15" cy="18" r="1.5" />
            </svg>
        </td>
        <td class="border px-2 py-1 text-center font-semibold batting-order"></td>
        <td class="border px-2 py-1 player-name-cell">
            <select name="player_names[]" class="player-name-input w-36 px-1 py-1 border rounded">
                <option value="">-</option>
                @foreach ($players as $player)
                    <option value="{{ $player->name }}">{{ $player->name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="stat_ids[]" value="">
            <input type="hidden" name="lineup_ids[]" value="">
        </td>
        <td class="border px-2 py-1">
            <select name="position[]" class="w-20 px-1 py-1 border rounded">
                <option value="">-</option>
                @foreach ($positions as $position)
                    <option value="{{ $position }}">{{ $position }}</option>
                @endforeach
            </select>
        </td>
        <td class="border px-2 py-1"><input type="number" name="ab[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="r[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="h[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="rbi[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="hr[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="bb[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="k[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" step="0.1" name="ip[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="ph[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="pr[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="er[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="pbb[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
        <td class="border px-2 py-1"><input type="number" name="pk[]" value="" class="w-12 px-1 py-1 border rounded text-center"></td>
    </tr>
</template>

<script>
    function renumberStatRows() {
        document.querySelectorAll('#stat-rows .batting-order').forEach((cell, index) => {
            cell.textContent = index + 1;
        });
    }

    function addPlayerStatRow() {
        const tbody = document.getElementById('stat-rows');
        const template = document.getElementById('empty-stat-row-template');
        tbody.appendChild(template.content.cloneNode(true));
        renumberStatRows();
    }

    const PREVIOUS_LINEUP = @json($previousLineupData);
    const LINEUP_POSITIONS = @json($positions);

    function applyLineupEntry(row, entry) {
        const nameInput = row.querySelector('.player-name-input');
        const positionSelect = row.querySelector('select[name="position[]"]');
        const name = entry ? entry.name : '';

        if (nameInput) {
            nameInput.value = name;
        }

        positionSelect.value = (entry && LINEUP_POSITIONS.includes(entry.position)) ? entry.position : '';
    }

    function usePreviousLineup() {
        if (!PREVIOUS_LINEUP.length) return;

        const tbody = document.getElementById('stat-rows');
        const hasSelection = Array.from(tbody.querySelectorAll('.player-name-input'))
            .some(input => (input.value || '').trim() !== '');

        if (hasSelection && !confirm('入力中の内容を前回のスタメンで上書きします。よろしいですか？')) {
            return;
        }

        while (tbody.querySelectorAll('tr').length < PREVIOUS_LINEUP.length) {
            addPlayerStatRow();
        }

        tbody.querySelectorAll('tr').forEach((row, index) => applyLineupEntry(row, PREVIOUS_LINEUP[index]));
    }
</script>

@include('games.partials.row-drag-script', ['tbodyId' => 'stat-rows'])

@endsection
