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

<h1 class="text-2xl font-bold mb-6">次の試合の予定</h1>

<form action="{{ route('games.upcoming.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">試合日</label>
            <input type="date" name="game_date" value="{{ old('game_date') }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">場所</label>
            <input type="text" name="location" value="{{ old('location') }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">相手チーム名</label>
            <input type="text" name="opponent" value="{{ old('opponent') }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>
    </div>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-2">スターティングラインナップ</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border bg-bf-cream">
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
                            <input type="text" name="player_names[]" value="{{ old('player_names.' . $i) }}" placeholder="姓 名（例：山田 太郎）" class="w-40 px-1 py-1 border rounded">
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
        <button type="button" id="add-lineup-row-btn" onclick="addLineupRow()" class="inline-block bg-bf-cream text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-bf-gold/20 transition">＋選手を追加</button>
        <p id="lineup-max-message" class="text-sm text-red-600 mt-1 hidden">選手は最大20人まで登録できます</p>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-bf-cream text-bf-navy px-6 py-2 rounded-full hover:bg-bf-gold/20 transition">
            保存する
        </button>
    </div>
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
        newRow.querySelectorAll('input').forEach(input => input.value = '');
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
</script>

@include('games.partials.player-name-autocomplete')

@endsection
