@extends('layouts.app')

@section('content')

@if ($errors->any())
<div class="bg-red-100 text-red-800 p-4 rounded mb-4">
    <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1 class="text-2xl font-bold mb-6">次の試合の予定を編集</h1>

<form action="{{ route('games.upcoming.update', $game) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-bf-navy mb-4">試合情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-bf-navy">
            <div>
                <label class="block text-sm font-medium">試合日</label>
                <input type="date" name="game_date" value="{{ old('game_date', $game->game_date) }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div>
                <label class="block text-sm font-medium">場所</label>
                <input type="text" name="location" value="{{ old('location', $game->location) }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>

            <div>
                <label class="block text-sm font-medium">相手チーム名</label>
                <input type="text" name="opponent" value="{{ old('opponent', $game->opponent) }}" required class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
            </div>
        </div>
    </div>

    <div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-bf-navy mb-4">スターティングラインナップ</h2>

        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full text-sm bg-bf-cream">
                <thead class="bg-bf-navy text-white">
                    <tr>
                        <th class="px-2 py-1 border w-8"></th>
                        <th class="px-2 py-1 border">打順</th>
                        <th class="px-2 py-1 border">選手名</th>
                        <th class="px-2 py-1 border">守備位置</th>
                    </tr>
                </thead>
                <tbody id="lineup-rows" class="text-gray-800">
                    @php
                        $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
                        $rowCount = max(9, $lineups->count());
                    @endphp

                    @for ($i = 0; $i < $rowCount; $i++)
                        @php
                            $lineup = $lineups->get($i);
                        @endphp
                        <tr>
                            <td class="border px-2 py-1 text-center drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none select-none">
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="9" cy="6" r="1.5" /><circle cx="15" cy="6" r="1.5" />
                                    <circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" />
                                    <circle cx="9" cy="18" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                                </svg>
                            </td>
                            <td class="border px-2 py-1 text-center font-semibold batting-order">{{ $i + 1 }}</td>
                            <td class="border px-2 py-1">
                                <input type="text" name="player_names[]"
                                    value="{{ old('player_names.' . $i, $lineup->player->name ?? '') }}"
                                    placeholder="選手名（例：山田）" class="w-40 px-1 py-1 border rounded">
                                <input type="hidden" name="lineup_ids[]" value="{{ old('lineup_ids.' . $i, $lineup->id ?? '') }}">
                            </td>
                            <td class="border px-2 py-1">
                                <select name="position[]" class="w-24 px-1 py-1 border rounded">
                                    <option value="">-</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position }}" @selected(old('position.' . $i, $lineup->position ?? '') === $position)>{{ $position }}</option>
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
            <p id="lineup-max-message" class="text-sm text-red-600 mt-1 hidden">選手は最大20人まで登録できます</p>
        </div>
    </div>

    <div>
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition">
            更新する
        </button>
    </div>
</form>

<form action="{{ route('games.destroy', $game) }}" method="POST" class="mt-6 pt-6 border-t border-gray-200"
    onsubmit="return confirm('本当に削除しますか？');">
    @csrf
    @method('DELETE')
    <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-6 py-2 rounded-lg hover:bg-red-100 transition">
        この試合を削除
    </button>
</form>

<script>
    const LINEUP_MAX_ROWS = 20;

    function addLineupRow() {
        const tbody = document.getElementById('lineup-rows');

        if (tbody.querySelectorAll('tr').length >= LINEUP_MAX_ROWS) {
            return;
        }

        const row = tbody.querySelector('tr:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input[type="text"], input[type="hidden"]').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        tbody.appendChild(newRow);
        renumberLineupRows();

        if (tbody.querySelectorAll('tr').length >= LINEUP_MAX_ROWS) {
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

    (function () {
        const tbody = document.getElementById('lineup-rows');
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
            renumberLineupRows();
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

@include('games.partials.player-name-autocomplete')

@endsection
