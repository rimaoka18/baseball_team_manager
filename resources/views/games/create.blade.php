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

<h1 class="text-2xl font-bold mb-6">試合結果の入力</h1>

<form action="{{ route('games.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm font-medium">自チーム得点</label>
                <input type="number" name="team_score" value="{{ old('team_score') }}" required class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">相手得点</label>
                <input type="number" name="opponent_score" value="{{ old('opponent_score') }}" required class="mt-1 w-full border rounded px-3 py-2">
            </div>
        </div>
    </div>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-2">選手成績</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
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
            <tbody id="player-rows">
                @php
                $statInputs = ['ab', 'r', 'h', 'rbi', 'hr', 'bb', 'k', 'ip', 'ph', 'pr', 'er', 'pbb', 'pk'];
                @endphp

                @for ($i = 0; $i < 9; $i++)
                    <tr>
                    {{-- Player Name --}}
                    <td class="border px-2 py-1">
                        <input type="text" name="player_names[]" value="{{ old('player_names.' . $i) }}" placeholder="姓 名（例：山田 太郎）" required class="w-32 px-1 py-1 border rounded">
                    </td>

                    {{-- Stat Inputs --}}
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
        <button type="button" onclick="addPlayerRow()" class="text-blue-600 hover:underline">＋選手を追加</button>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
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
        tbody.appendChild(newRow);
    }
</script>
@endsection
