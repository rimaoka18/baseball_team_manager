@extends('layouts.app')

@section('content')

<div class="space-y-6">

<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold text-bf-cream">選手詳細</h2>
	<a href="{{ route('roster.index') }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full transition">
		選手一覧に戻る
	</a>
</div>

@if (session('success'))
	<div data-auto-dismiss class="bg-bf-cream text-bf-navy border border-bf-gold/50 p-3 rounded-xl font-medium">{{ session('success') }}</div>
@endif

@include('players.partials.player-stats-card', ['player' => $player])

<a href="{{ route('roster.players.edit', $player) }}"
	class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-5 py-2 rounded-full transition">
	編集する
</a>

</div>

@endsection
