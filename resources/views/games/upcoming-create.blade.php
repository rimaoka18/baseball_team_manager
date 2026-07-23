@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<h1 class="text-2xl font-bold mb-6">試合予定を追加</h1>

<form action="{{ route('games.upcoming.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-bf-navy mb-4">試合情報</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-bf-navy">
            <div class="min-w-0">
                <label class="block text-sm font-medium">試合日</label>
                <input type="date" name="game_date" value="{{ old('game_date') }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-medium">開始時刻</label>
                <input type="time" name="game_time" value="{{ old('game_time') }}"
                    class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800 transition focus:outline-none focus:ring-2 focus:ring-bf-gold/50 focus:border-bf-navy">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-medium">場所</label>
                <input type="text" name="location" value="{{ old('location') }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-medium">相手チーム名</label>
                <input type="text" name="opponent" value="{{ old('opponent') }}" required class="mt-1 w-full min-w-0 max-w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>
        </div>
    </div>

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-bf-navy">スタメン登録</h2>
            @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
        </div>

        @if ($players->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white/50 px-4 py-8 text-center">
                <p class="text-gray-800 font-semibold mb-1">選手がいません</p>
                <p class="text-sm text-gray-500 mb-4">先に選手を追加してから、スタメンを選んでください</p>
                <a href="{{ route('roster.index') }}"
                    class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-navy-light transition">
                    選手を開く
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                <table class="min-w-full text-sm bg-bf-cream">
                    <thead class="bg-bf-navy text-white">
                        <tr>
                            <th class="px-2 py-1 border">打順</th>
                            <th class="px-2 py-1 border">選手名</th>
                            <th class="px-2 py-1 border">守備位置</th>
                        </tr>
                    </thead>
                    <tbody id="lineup-rows" class="text-gray-800">
                        @php
                            $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
                        @endphp

                        @for ($i = 0; $i < 9; $i++)
                            <tr>
                                <td class="border px-2 py-1 text-center font-semibold batting-order">{{ $i + 1 }}</td>
                                <td class="border px-2 py-1">
                                    <select name="player_ids[]" class="w-44 px-1 py-1 border rounded">
                                        <option value="">-</option>
                                        @foreach ($players as $player)
                                            <option value="{{ $player->id }}" @selected((string) old('player_ids.' . $i) === (string) $player->id)>{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border px-2 py-1">
                                    <select name="position[]" class="w-24 px-1 py-1 border rounded">
                                        <option value="">-</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position }}" @selected(old('position.' . $i) === $position)>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" id="add-lineup-row-btn" onclick="addLineupRow()" class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-navy-light transition">＋選手を追加</button>
                <p id="lineup-max-message" class="text-sm text-bf-danger mt-1 hidden">選手は最大20人まで登録できます</p>
            </div>
        @endif
    </div>

    <div>
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition" @disabled($players->isEmpty())>
            保存する
        </button>
    </div>
</form>

@if ($players->isNotEmpty())
<script>
    const LINEUP_MAX_ROWS = 20;

    function addLineupRow() {
        const tbody = document.getElementById('lineup-rows');

        if (tbody.querySelectorAll('tr').length >= LINEUP_MAX_ROWS) {
            return;
        }

        const row = tbody.querySelector('tr:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        tbody.appendChild(newRow);

        tbody.querySelectorAll('.batting-order').forEach((cell, index) => {
            cell.textContent = index + 1;
        });

        if (tbody.querySelectorAll('tr').length >= LINEUP_MAX_ROWS) {
            document.getElementById('lineup-max-message').classList.remove('hidden');
            document.getElementById('add-lineup-row-btn').disabled = true;
            document.getElementById('add-lineup-row-btn').classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
        }
    }

    @php
        $positions = $positions ?? ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
    @endphp
    const PREVIOUS_LINEUP = @json($previousLineupData);
    const LINEUP_POSITIONS = @json($positions);

    function applyLineupEntry(row, entry) {
        const playerSelect = row.querySelector('select[name="player_ids[]"]');
        const positionSelect = row.querySelector('select[name="position[]"]');
        playerSelect.value = entry ? String(entry.id) : '';
        positionSelect.value = (entry && LINEUP_POSITIONS.includes(entry.position)) ? entry.position : '';
    }

    function usePreviousLineup() {
        if (!PREVIOUS_LINEUP.length) return;

        const tbody = document.getElementById('lineup-rows');
        const hasSelection = Array.from(tbody.querySelectorAll('select[name="player_ids[]"]'))
            .some(select => select.value !== '');

        if (hasSelection && !confirm('入力中の内容を前回のスタメンで上書きします。よろしいですか？')) {
            return;
        }

        while (tbody.querySelectorAll('tr').length < PREVIOUS_LINEUP.length) {
            addLineupRow();
        }

        tbody.querySelectorAll('tr').forEach((row, index) => applyLineupEntry(row, PREVIOUS_LINEUP[index]));
    }
</script>
@endif

@endsection
