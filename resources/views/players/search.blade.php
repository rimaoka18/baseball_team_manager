@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-center text-bf-cream">プレイヤー検索</h1>

    <!-- Search Form -->
    <form method="GET" action="{{ route('players.search') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-center mb-8">
        <input
            type="text"
            name="q"
            placeholder="名前を入力（例：山田 太郎）"
            value="{{ $query }}"
            class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-64 bg-bf-cream text-bf-navy placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-bf-gold">
        <button
            type="submit"
            class="bg-bf-cream text-bf-navy px-4 py-2 rounded-full hover:bg-bf-gold/20 transition w-full sm:w-auto">
            検索
        </button>
    </form>

    <!-- Search Result -->
    @if ($player)
    <div class="bg-bf-cream shadow-md rounded-xl p-6 text-bf-navy">
        <h2 class="text-xl font-semibold mb-4 text-bf-navy">{{ $player->name }} の成績</h2>

        @php
        $ab = $player->gameStats->sum('at_bats');
        $hits = $player->gameStats->sum('hits');
        $avg = $ab > 0 ? round($hits / $ab, 3) : 0;

        $ip = $player->gameStats->sum('innings_pitched');
        $er = $player->gameStats->sum('earned_runs');
        $era = ($ip > 0 && $er !== null) ? round(($er * 9) / $ip, 2) : null;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">打撃成績</h3>
                <p class="mb-1 text-gray-800"><span class="font-medium">打率：</span>{{ number_format($avg, 3) }}</p>
                <p class="mb-1 text-gray-800"><span class="font-medium">打数：</span>{{ $ab }}</p>
                <p class="mb-1 text-gray-800"><span class="font-medium">安打：</span>{{ $hits }}</p>
                <p class="mb-1 text-gray-800"><span class="font-medium">ホームラン：</span>{{ $player->gameStats->sum('home_runs') }}</p>
            </div>

            @if ($ip >= 0)
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">投手成績</h3>
                <p class="mb-1 text-gray-800"><span class="font-medium">防御率：</span>{{ number_format($era, 2) }}</p>
                <p class="mb-1 text-gray-800"><span class="font-medium">投球回：</span>{{ $ip }}</p>
                <p class="mb-1 text-gray-800"><span class="font-medium">奪三振：</span>{{ $player->gameStats->sum('pitching_strikeouts') }}</p>
            </div>
            @endif
        </div>
    </div>
    @elseif ($query)
    <p class="text-center text-bf-danger font-semibold bg-bf-cream rounded-xl px-4 py-3">「{{ $query }}」の選手が見つかりませんでした。</p>
    @endif

    <div class="mt-6">
        <a href="{{ route('roster.index') }}"
            class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-2 rounded-full shadow-sm transition">
            ← 選手に戻る
        </a>
    </div>

</div>
@endsection
