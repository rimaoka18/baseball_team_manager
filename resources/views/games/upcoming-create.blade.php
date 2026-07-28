@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<div class="max-w-[980px] mx-auto">

<h1 class="font-heading text-3xl font-extrabold mb-6">試合予定を追加</h1>

<form action="{{ route('games.upcoming.store') }}" method="POST">
    @csrf

    <h6 class="bf-kicker mb-3">試合情報</h6>
    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">試合日</label>
            <input type="date" name="game_date" value="{{ old('game_date') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">開始時刻</label>
            <input type="time" name="game_time" value="{{ old('game_time') }}" class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">場所</label>
            <input type="text" name="location" value="{{ old('location') }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手チーム名</label>
            <input type="text" name="opponent" value="{{ old('opponent') }}" required class="bf-input">
        </div>
    </div>

    <hr class="border-t-2 border-bf-divider my-8">

    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-4">
        <h6 class="bf-kicker m-0">スタメン登録</h6>
        @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
    </div>

    @if ($players->isEmpty())
        <div class="border border-bf-divider px-4 py-8 text-center">
            <p class="font-heading font-extrabold mb-1">選手がいません</p>
            <p class="text-sm text-bf-ink/60 mb-4">先に選手を追加してから、スタメンを選んでください</p>
            <a href="{{ route('roster.index') }}" class="bf-btn bf-btn-primary">選手を開く</a>
        </div>
    @else
        @php
            $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
        @endphp

        <div>
            <div class="flex items-center gap-3 pb-2 border-b-2 border-bf-divider bf-kicker">
                <div class="w-7 text-center shrink-0">打順</div>
                <div class="flex-1">選手名</div>
                <div class="w-[100px] shrink-0">守備位置</div>
            </div>

            <div id="lineup-rows">
                @for ($i = 0; $i < 9; $i++)
                    <div class="lineup-row bf-lineup-row">
                        <div class="w-7 text-center shrink-0 font-heading font-extrabold text-[15px] text-bf-navy batting-order">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-[140px]">
                            <select name="player_ids[]" class="bf-select">
                                <option value="">-</option>
                                @foreach ($players as $player)
                                    <option value="{{ $player->id }}" @selected((string) old('player_ids.' . $i) === (string) $player->id)>{{ $player->rosterLabel() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-[100px] shrink-0">
                            <select name="position[]" class="bf-select">
                                <option value="">-</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position }}" @selected(old('position.' . $i) === $position)>{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="mt-4">
            <button type="button" id="add-lineup-row-btn" onclick="addLineupRow()" class="bf-btn bf-btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                選手を追加
            </button>
            <p id="lineup-max-message" class="text-sm text-bf-danger mt-1 hidden">選手は最大20人まで登録できます</p>
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
    const LINEUP_MAX_ROWS = 20;

    function addLineupRow() {
        const container = document.getElementById('lineup-rows');

        if (container.querySelectorAll('.lineup-row').length >= LINEUP_MAX_ROWS) {
            return;
        }

        const row = container.querySelector('.lineup-row:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        container.appendChild(newRow);

        container.querySelectorAll('.batting-order').forEach((cell, index) => {
            cell.textContent = index + 1;
        });

        if (container.querySelectorAll('.lineup-row').length >= LINEUP_MAX_ROWS) {
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

        const container = document.getElementById('lineup-rows');
        const hasSelection = Array.from(container.querySelectorAll('select[name="player_ids[]"]'))
            .some(select => select.value !== '');

        if (hasSelection && !confirm('入力中の内容を前回のスタメンで上書きします。よろしいですか？')) {
            return;
        }

        while (container.querySelectorAll('.lineup-row').length < PREVIOUS_LINEUP.length) {
            addLineupRow();
        }

        container.querySelectorAll('.lineup-row').forEach((row, index) => applyLineupEntry(row, PREVIOUS_LINEUP[index]));
    }
</script>
@endif

@endsection
