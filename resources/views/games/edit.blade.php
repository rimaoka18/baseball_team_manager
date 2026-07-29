@extends('layouts.app')

@section('content')

@include('partials.validation-errors')

<div class="max-w-[980px] mx-auto">

<h1 class="font-heading text-3xl font-extrabold mb-6">試合編集</h1>

<form action="{{ route('games.update', $game) }}" method="POST">
    @csrf
    @method('PUT')

    <h6 class="bf-kicker mb-3">試合情報</h6>
    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">試合日</label>
            <input type="date" name="game_date" value="{{ $game->game_date }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">場所</label>
            <input type="text" name="location" value="{{ $game->location }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手チーム名</label>
            <input type="text" name="opponent" value="{{ $game->opponent }}" required class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">自チーム得点</label>
            <input type="number" name="team_score" value="{{ $game->team_score }}" class="bf-input">
        </div>
        <div>
            <label class="block text-xs text-bf-ink/60 mb-1">相手得点</label>
            <input type="number" name="opponent_score" value="{{ $game->opponent_score }}" class="bf-input">
        </div>
    </div>

    <hr class="border-t-2 border-bf-divider my-8">

    @php
        $positions = ['投', '捕', '一', '二', '三', '遊', '左', '中', '右', '指'];
    @endphp

    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-4">
        <h6 class="bf-kicker m-0">選手成績</h6>
        @include('games.partials.use-previous-lineup-button', ['previousGame' => $previousGame])
    </div>

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
            <tbody id="stat-rows">
                @foreach ($stats as $index => $stat)
                    @php $playerName = $stat->player->name ?? ''; @endphp
                    <tr class="border-b border-bf-divider">
                        <td class="px-2 py-1 text-center">
                            <div class="drag-handle bf-drag-handle mx-auto touch-none select-none" title="ドラッグして並び替え">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/></svg>
                            </div>
                        </td>
                        <td class="px-2 py-1 text-center font-heading font-extrabold text-bf-navy batting-order">{{ $stat->batting_order ?? ($index + 1) }}</td>
                        <td class="px-2 py-1 player-name-cell">
                            @if ($playerName !== '')
                                <span class="player-name-label font-medium whitespace-nowrap">{{ $playerName }}</span>
                                <input type="hidden" name="player_names[]" class="player-name-input" value="{{ $playerName }}">
                            @else
                                <select name="player_names[]" class="player-name-input bf-select w-36">
                                    <option value="">-</option>
                                    @foreach ($players as $player)
                                        <option value="{{ $player->name }}">{{ $player->rosterLabel() }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <input type="hidden" name="stat_ids[]" value="{{ $stat->id }}">
                            <input type="hidden" name="lineup_ids[]" value="{{ $stat->lineup_id }}">
                        </td>
                        <td class="px-2 py-1">
                            <select name="position[]" class="bf-select w-20">
                                <option value="">-</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position }}" @selected(($stat->position ?? '') === $position)>{{ $position }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-1 py-1"><input type="number" name="ab[]" value="{{ $stat->at_bats }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="r[]" value="{{ $stat->runs }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="h[]" value="{{ $stat->hits }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="rbi[]" value="{{ $stat->rbi }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="hr[]" value="{{ $stat->home_runs }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="bb[]" value="{{ $stat->walks }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="k[]" value="{{ $stat->strikeouts }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" step="0.1" name="ip[]" value="{{ $stat->innings_pitched }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="ph[]" value="{{ $stat->hits_allowed }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="pr[]" value="{{ $stat->pr }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="er[]" value="{{ $stat->earned_runs }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="pbb[]" value="{{ $stat->pitching_walks }}" class="bf-input w-12 px-1 text-center"></td>
                        <td class="px-1 py-1"><input type="number" name="pk[]" value="{{ $stat->pitching_strikeouts }}" class="bf-input w-12 px-1 text-center"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <button type="button" id="add-stat-row-btn" onclick="addPlayerStatRow()" class="bf-btn bf-btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            選手を追加
        </button>
    </div>

    <hr class="border-t-2 border-bf-divider my-8">

    <div>
        <button type="submit" class="bf-btn bf-btn-primary px-8">
            更新する
        </button>
    </div>
</form>

</div>

<template id="empty-stat-row-template">
    <tr class="border-b border-bf-divider">
        <td class="px-2 py-1 text-center">
            <div class="drag-handle bf-drag-handle mx-auto touch-none select-none" title="ドラッグして並び替え">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/></svg>
            </div>
        </td>
        <td class="px-2 py-1 text-center font-heading font-extrabold text-bf-navy batting-order"></td>
        <td class="px-2 py-1 player-name-cell">
            <select name="player_names[]" class="player-name-input bf-select w-36">
                <option value="">-</option>
                @foreach ($players as $player)
                    <option value="{{ $player->name }}">{{ $player->rosterLabel() }}</option>
                @endforeach
            </select>
            <input type="hidden" name="stat_ids[]" value="">
            <input type="hidden" name="lineup_ids[]" value="">
        </td>
        <td class="px-2 py-1">
            <select name="position[]" class="bf-select w-20">
                <option value="">-</option>
                @foreach ($positions as $position)
                    <option value="{{ $position }}">{{ $position }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-1 py-1"><input type="number" name="ab[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="r[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="h[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="rbi[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="hr[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="bb[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="k[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" step="0.1" name="ip[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="ph[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="pr[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="er[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="pbb[]" value="" class="bf-input w-12 px-1 text-center"></td>
        <td class="px-1 py-1"><input type="number" name="pk[]" value="" class="bf-input w-12 px-1 text-center"></td>
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

    (function () {
        const tbody = document.getElementById('stat-rows');
        let draggedRow = null;
        let draggedHandle = null;
        let pointerId = null;

        function rowAtPoint(x, y) {
            const el = document.elementFromPoint(x, y);
            const row = el && el.closest('tr');
            return (row && row.parentElement === tbody) ? row : null;
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
            renumberStatRows();
        }

        tbody.addEventListener('pointerdown', (e) => {
            const handle = e.target.closest('.drag-handle');
            if (!handle) return;

            draggedRow = handle.closest('tr');
            draggedHandle = handle;
            pointerId = e.pointerId;
            handle.setPointerCapture(pointerId);
            draggedRow.classList.add('opacity-50', 'shadow-lg', 'relative', 'z-10');
            e.preventDefault();
        });

        tbody.addEventListener('pointermove', (e) => {
            if (!draggedRow || e.pointerId !== pointerId) return;
            e.preventDefault();

            const targetRow = rowAtPoint(e.clientX, e.clientY);
            if (!targetRow || targetRow === draggedRow) return;

            const rect = targetRow.getBoundingClientRect();
            const isAfter = (e.clientY - rect.top) > rect.height / 2;
            tbody.insertBefore(draggedRow, isAfter ? targetRow.nextSibling : targetRow);
        });

        tbody.addEventListener('pointerup', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });

        tbody.addEventListener('pointercancel', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });
    })();
</script>

@endsection
