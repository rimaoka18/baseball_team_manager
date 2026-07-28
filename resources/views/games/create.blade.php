@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<div class="max-w-[980px] mx-auto">

<h1 class="font-heading text-3xl font-extrabold mb-6">試合結果の入力</h1>

<form action="{{ route('games.store') }}" method="POST">
    @csrf

    <h6 class="bf-kicker mb-3">試合情報</h6>
    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">試合日</label>
            <input type="date" name="game_date" value="{{ old('game_date') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">場所</label>
            <input type="text" name="location" value="{{ old('location') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手チーム名</label>
            <input type="text" name="opponent" value="{{ old('opponent') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">自チーム得点</label>
            <input type="number" name="team_score" value="{{ old('team_score') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手得点</label>
            <input type="number" name="opponent_score" value="{{ old('opponent_score') }}" required class="bf-input">
        </div>
    </div>

    <hr class="border-t-2 border-bf-divider my-8">

    @php
        $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
    @endphp

    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-4">
        <h6 class="bf-kicker m-0">選手成績</h6>
        @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
    </div>

    @if ($players->isEmpty())
        <div class="border border-bf-divider px-4 py-8 text-center">
            <p class="font-heading font-extrabold mb-1">選手がいません</p>
            <p class="text-sm text-bf-ink/60 mb-4">先に選手を追加してから、成績を入力してください</p>
            <a href="{{ route('roster.index') }}" class="bf-btn bf-btn-primary">選手を開く</a>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b-2 border-bf-divider bf-kicker">
                    <th class="px-2 py-1 w-8"></th>
                    <th class="px-2 py-1 text-left">打順</th>
                    <th class="px-2 py-1 text-left">選手名</th>
                    <th class="px-2 py-1 text-left">守備</th>
                    <th class="px-2 py-1">AB</th>
                    <th class="px-2 py-1">R</th>
                    <th class="px-2 py-1">H</th>
                    <th class="px-2 py-1">RBI</th>
                    <th class="px-2 py-1">HR</th>
                    <th class="px-2 py-1">BB</th>
                    <th class="px-2 py-1">K</th>
                    <th class="px-2 py-1">IP</th>
                    <th class="px-2 py-1">H(P)</th>
                    <th class="px-2 py-1">R(P)</th>
                    <th class="px-2 py-1">ER</th>
                    <th class="px-2 py-1">BB(P)</th>
                    <th class="px-2 py-1">K(P)</th>
                </tr>
            </thead>
            <tbody id="player-rows">
                @php
                $statInputs = ['ab', 'r', 'h', 'rbi', 'hr', 'bb', 'k', 'ip', 'ph', 'pr', 'er', 'pbb', 'pk'];
                @endphp

                @for ($i = 0; $i < 9; $i++)
                    <tr class="border-b border-bf-divider">
                    <td class="px-2 py-1 text-center">
                        <div class="drag-handle bf-drag-handle mx-auto touch-none select-none" title="ドラッグして並び替え">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/></svg>
                        </div>
                    </td>
                    <td class="px-2 py-1 text-center font-heading font-extrabold text-bf-navy batting-order">{{ $i + 1 }}</td>
                    <td class="px-2 py-1">
                        <select name="player_ids[]" class="bf-select w-36">
                            <option value="">-</option>
                            @foreach ($players as $player)
                                <option value="{{ $player->id }}" @selected((string) old('player_ids.' . $i) === (string) $player->id)>{{ $player->rosterLabel() }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-2 py-1">
                        <select name="position[]" class="bf-select w-20">
                            <option value="">-</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position }}" @selected(old('position.' . $i) === $position)>{{ $position }}</option>
                            @endforeach
                        </select>
                    </td>

                    @foreach ($statInputs as $stat)
                    <td class="px-1 py-1">
                        <input
                            type="number"
                            name="{{ $stat }}[]"
                            value="{{ old($stat . '.' . $i) }}"
                            step="{{ $stat === 'ip' ? '0.1' : '1' }}"
                            class="bf-input w-12 px-1 text-center">
                    </td>
                    @endforeach
                    </tr>
                    @endfor
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        <button type="button" onclick="addPlayerRow()" class="bf-btn bf-btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            選手を追加
        </button>
    </div>
    @endif

    <hr class="border-t-2 border-bf-divider my-8">

    <div>
        <button type="submit" class="bf-btn bf-btn-primary px-8" @disabled($players->isEmpty())>
            保存する
        </button>
    </div>
</form>

</div>

@if ($players->isNotEmpty())
<script>
    function addPlayerRow() {
        const tbody = document.getElementById('player-rows');
        const row = tbody.querySelector('tr:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        tbody.appendChild(newRow);
        tbody.querySelectorAll('.batting-order').forEach((cell, index) => {
            cell.textContent = index + 1;
        });
    }

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

        const tbody = document.getElementById('player-rows');
        const hasSelection = Array.from(tbody.querySelectorAll('select[name="player_ids[]"]'))
            .some(select => select.value !== '');

        if (hasSelection && !confirm('入力中の内容を前回のスタメンで上書きします。よろしいですか？')) {
            return;
        }

        while (tbody.querySelectorAll('tr').length < PREVIOUS_LINEUP.length) {
            addPlayerRow();
        }

        tbody.querySelectorAll('tr').forEach((row, index) => applyLineupEntry(row, PREVIOUS_LINEUP[index]));
    }
</script>

@include('games.partials.row-drag-script', ['tbodyId' => 'player-rows'])
@endif

@endsection
