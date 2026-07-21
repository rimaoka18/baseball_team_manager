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

<h1 class="text-2xl font-bold mb-6">試合編集</h1>

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
                <input type="number" name="team_score" value="{{ $game->team_score }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">相手得点</label>
                <input type="number" name="opponent_score" value="{{ $game->opponent_score }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>
        </div>
    </div>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-2">選手成績</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border bg-bf-cream">
            <thead class="bg-bf-navy text-white">
                <tr>
                    <th class="px-2 py-1 border w-8"></th>
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
            <tbody id="stat-rows" class="text-gray-800">
                @foreach ($stats as $stat)
                    <tr>
                        <td class="border px-2 py-1 text-center drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none select-none">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="9" cy="6" r="1.5" /><circle cx="15" cy="6" r="1.5" />
                                <circle cx="9" cy="12" r="1.5" /><circle cx="15" cy="12" r="1.5" />
                                <circle cx="9" cy="18" r="1.5" /><circle cx="15" cy="18" r="1.5" />
                            </svg>
                        </td>
                        <td class="border px-2 py-1">
                            <input type="text" name="player_names[]" value="{{ $stat->player->name }}"
                                   class="w-32 border rounded px-2 py-1" required>
                            <input type="hidden" name="stat_ids[]" value="{{ $stat->id }}">
                            <input type="hidden" name="lineup_ids[]" value="{{ $stat->lineup_id }}">
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
        <button type="button" id="add-stat-row-btn" onclick="addPlayerStatRow()" class="inline-block bg-bf-cream text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-gold/20 transition">＋選手を追加</button>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition">
            更新する
        </button>
    </div>
</form>

@include('games.partials.player-name-autocomplete')

<script>
    function addPlayerStatRow() {
        const tbody = document.getElementById('stat-rows');
        const row = tbody.querySelector('tr:last-child');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input[type="text"], input[type="hidden"], input[type="number"]').forEach(input => input.value = '');
        tbody.appendChild(newRow);
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
