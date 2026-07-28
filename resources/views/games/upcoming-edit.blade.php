@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<div class="max-w-[980px] mx-auto">

<h1 class="font-heading text-3xl font-extrabold mb-6">試合予定を編集</h1>

<form id="upcoming-edit-form" action="{{ route('games.upcoming.update', $game) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="from" value="{{ request('from') }}">

    <h6 class="bf-kicker mb-3">試合情報</h6>
    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">試合日</label>
            <input type="date" name="game_date" value="{{ old('game_date', $game->game_date) }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">開始時刻</label>
            <input type="time" name="game_time" value="{{ old('game_time', $game->game_time_formatted) }}" class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">場所</label>
            <input type="text" name="location" value="{{ old('location', $game->location) }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手チーム名</label>
            <input type="text" name="opponent" value="{{ old('opponent', $game->opponent) }}" required class="bf-input">
        </div>
    </div>

    <hr class="border-t-2 border-bf-divider my-8">

    @php
        $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
    @endphp

    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-4">
        <h6 class="bf-kicker m-0">スタメン編集</h6>
        @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
    </div>

    @if ($players->isEmpty())
        <div class="border border-bf-divider px-4 py-8 text-center">
            <p class="font-heading font-extrabold mb-1">選手がいません</p>
            <p class="text-sm text-bf-ink/60 mb-4">先に選手を追加してから、スタメンを選んでください</p>
            <a href="{{ route('roster.index') }}" class="bf-btn bf-btn-primary">選手を開く</a>
        </div>
    @else
        <div>
            <div class="flex items-center gap-3 pb-2 border-b-2 border-bf-divider bf-kicker">
                <div class="w-8 shrink-0"></div>
                <div class="w-7 text-center shrink-0">打順</div>
                <div class="flex-1">選手名</div>
                <div class="w-[100px] shrink-0">守備位置</div>
            </div>

            <div id="lineup-rows">
                @php
                    $rowCount = max(9, $lineups->count());
                @endphp

                @for ($i = 0; $i < $rowCount; $i++)
                    @php
                        $lineup = $lineups->get($i);
                    @endphp
                    <div class="lineup-row bf-lineup-row">
                        <div class="drag-handle bf-drag-handle shrink-0 touch-none select-none" title="ドラッグして並び替え">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/></svg>
                        </div>
                        <div class="w-7 text-center shrink-0 font-heading font-extrabold text-[15px] text-bf-navy batting-order">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-[140px]">
                            <select name="player_ids[]" class="bf-select">
                                <option value="">-</option>
                                @foreach ($players as $player)
                                    <option value="{{ $player->id }}" @selected((string) old('player_ids.' . $i, (string) ($lineup?->player_id ?? '')) === (string) $player->id)>{{ $player->rosterLabel() }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="lineup_ids[]" value="{{ old('lineup_ids.' . $i, $lineup?->id ?? '') }}">
                        </div>
                        <div class="w-[100px] shrink-0">
                            <select name="position[]" class="bf-select">
                                <option value="">-</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position }}" @selected(old('position.' . $i, $lineup?->position ?? '') === $position)>{{ $position }}</option>
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
</form>

<form id="delete-game-form" action="{{ route('games.destroy', $game) }}" method="POST"
    onsubmit="return confirm('本当に削除しますか？');">
    @csrf
    @method('DELETE')
</form>

<div class="flex items-center justify-between gap-4 flex-wrap">
    <button type="submit" form="upcoming-edit-form" class="bf-btn bf-btn-primary px-8">
        更新する
    </button>
    <button type="submit" form="delete-game-form" class="bf-btn bf-btn-ghost">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        この試合を削除
    </button>
</div>

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
        newRow.querySelectorAll('input[type="hidden"]').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        container.appendChild(newRow);
        renumberLineupRows();

        if (container.querySelectorAll('.lineup-row').length >= LINEUP_MAX_ROWS) {
            document.getElementById('lineup-max-message').classList.remove('hidden');
            document.getElementById('add-lineup-row-btn').disabled = true;
            document.getElementById('add-lineup-row-btn').classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
        }
    }

    function renumberLineupRows() {
        document.querySelectorAll('#lineup-rows .batting-order').forEach((cell, index) => {
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

    (function () {
        const container = document.getElementById('lineup-rows');
        let draggedRow = null;
        let draggedHandle = null;
        let pointerId = null;

        function rowAtPoint(x, y) {
            const el = document.elementFromPoint(x, y);
            const row = el && el.closest('.lineup-row');
            return (row && row.parentElement === container) ? row : null;
        }

        function endDrag() {
            if (draggedHandle && pointerId !== null && draggedHandle.hasPointerCapture(pointerId)) {
                draggedHandle.releasePointerCapture(pointerId);
            }
            if (draggedRow) {
                draggedRow.classList.remove('opacity-50', 'shadow-lg', 'relative', 'z-10');
            }
            draggedRow = null;
            draggedHandle = null;
            pointerId = null;
            renumberLineupRows();
        }

        container.addEventListener('pointerdown', (e) => {
            const handle = e.target.closest('.drag-handle');
            if (!handle) return;

            draggedRow = handle.closest('.lineup-row');
            draggedHandle = handle;
            pointerId = e.pointerId;
            handle.setPointerCapture(pointerId);
            draggedRow.classList.add('opacity-50', 'shadow-lg', 'relative', 'z-10');
            e.preventDefault();
        });

        container.addEventListener('pointermove', (e) => {
            if (!draggedRow || e.pointerId !== pointerId) return;
            e.preventDefault();

            const targetRow = rowAtPoint(e.clientX, e.clientY);
            if (!targetRow || targetRow === draggedRow) return;

            const rect = targetRow.getBoundingClientRect();
            const isAfter = (e.clientY - rect.top) > rect.height / 2;
            container.insertBefore(draggedRow, isAfter ? targetRow.nextSibling : targetRow);
        });

        container.addEventListener('pointerup', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });

        container.addEventListener('pointercancel', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });
    })();
</script>
@endif

@endsection
