@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<h1 class="text-2xl font-bold mb-6">試合結果の入力</h1>

<form action="{{ route('games.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-bf-navy mb-4">試合情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-bf-navy">
            <div>
                <label class="block text-sm font-medium">試合日</label>
                <input type="date" name="game_date" value="{{ old('game_date') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div>
                <label class="block text-sm font-medium">場所</label>
                <input type="text" name="location" value="{{ old('location') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div>
                <label class="block text-sm font-medium">相手チーム名</label>
                <input type="text" name="opponent" value="{{ old('opponent') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium">自チーム得点</label>
                    <input type="number" name="team_score" value="{{ old('team_score') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium">相手得点</label>
                    <input type="number" name="opponent_score" value="{{ old('opponent_score') }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
                </div>
            </div>
        </div>
    </div>

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
                <tbody id="player-rows" class="text-gray-800">
                    @php
                    $statInputs = ['ab', 'r', 'h', 'rbi', 'hr', 'bb', 'k', 'ip', 'ph', 'pr', 'er', 'pbb', 'pk'];
                    $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
                    @endphp

                    @for ($i = 0; $i < 9; $i++)
                        <tr>
                        <td class="border px-2 py-1 text-center drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none select-none">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="9" cy="6" r="1.5" /><circle cx="15" cy="6" r="1.5" />
                                <circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" />
                                <circle cx="9" cy="18" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                            </svg>
                        </td>
                        <td class="border px-2 py-1">
                            <input type="text" name="player_names[]" value="{{ old('player_names.' . $i) }}" placeholder="選手名（例：山田）" required class="w-32 px-1 py-1 border rounded">
                        </td>
                        <td class="border px-2 py-1">
                            <select name="position[]" class="w-20 px-1 py-1 border rounded">
                                <option value="">-</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position }}" @selected(old('position.' . $i) === $position)>{{ $position }}</option>
                                @endforeach
                            </select>
                        </td>

                        @foreach ($statInputs as $stat)
                        <td class="border px-2 py-1">
                            <input
                                type="number"
                                name="{{ $stat }}[]"
                                value="{{ old($stat . '.' . $i) }}"
                                step="{{ $stat === 'ip' ? '0.1' : '1' }}"
                                class="w-12 px-1 py-1 border rounded text-center">
                        </td>
                        @endforeach
                        </tr>
                        @endfor
                </tbody>

            </table>
        </div>

        <div class="mt-4">
            <button type="button" onclick="addPlayerRow()" class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-navy-light transition">＋選手を追加</button>
        </div>
    </div>

    <div>
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition">
            保存する
        </button>
    </div>
</form>

<script>
    function addPlayerRow() {
        const tbody = document.getElementById('player-rows');
        const row = tbody.querySelector('tr:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        tbody.appendChild(newRow);
    }

    @php
        $previousLineupData = $previousGame
            ? $previousGame->lineups->map(fn ($l) => ['name' => $l->player->name ?? '', 'position' => $l->position])->values()
            : collect();
    @endphp
    const PREVIOUS_LINEUP = @json($previousLineupData);
    const LINEUP_POSITIONS = @json($positions);

    function applyLineupEntry(row, entry) {
        const nameInput = row.querySelector('input[name="player_names[]"]');
        const positionSelect = row.querySelector('select[name="position[]"]');
        nameInput.value = entry ? entry.name : '';
        positionSelect.value = (entry && LINEUP_POSITIONS.includes(entry.position)) ? entry.position : '';
    }

    function usePreviousLineup() {
        if (!PREVIOUS_LINEUP.length) return;

        const tbody = document.getElementById('player-rows');
        const hasInput = Array.from(tbody.querySelectorAll('input[name="player_names[]"]'))
            .some(input => input.value.trim() !== '');

        if (hasInput && !confirm('入力中の内容を前回のスタメンで上書きします。よろしいですか？')) {
            return;
        }

        while (tbody.querySelectorAll('tr').length < PREVIOUS_LINEUP.length) {
            addPlayerRow();
        }

        tbody.querySelectorAll('tr').forEach((row, index) => applyLineupEntry(row, PREVIOUS_LINEUP[index]));
    }
</script>

@include('games.partials.row-drag-script', ['tbodyId' => 'player-rows'])
@include('games.partials.player-name-autocomplete')

@endsection
