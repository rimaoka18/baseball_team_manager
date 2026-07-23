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
    @include('players.partials.player-stats-card', ['player' => $player])
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
