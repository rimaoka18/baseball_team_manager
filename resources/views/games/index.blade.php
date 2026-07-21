@extends('layouts.app')

@section('content')

<div class="space-y-6">

<!-- Header and Add Button -->
<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold">試合一覧</h2>
	<a href="{{ route('games.create') }}">
		<button class="bg-bf-cream text-bf-navy px-4 py-2 rounded-full hover:bg-bf-gold/20 transition">
			＋ 試合結果を追加
		</button>
	</a>
</div>

<!-- Games List -->
<div class="max-w-xl mx-auto">
	@if ($upcomingGames->isNotEmpty())
		<div class="flex items-baseline justify-between mt-6 mb-2 pb-2 border-b border-white/10">
			<h3 class="text-lg font-bold text-white">予定の試合</h3>
			<span class="text-sm text-gray-400">全{{ $upcomingGames->count() }}試合</span>
		</div>
		@foreach ($upcomingGames as $game)
			@include('games.partials.game-card', ['game' => $game])
		@endforeach
	@endif

	@if ($completedGames->isNotEmpty())
		<div class="flex items-baseline justify-between mt-6 mb-2 pb-2 border-b border-white/10">
			<h3 class="text-lg font-bold text-white">試合結果</h3>
			<span class="text-sm text-gray-400">{{ $completedGames->count() }}試合</span>
		</div>
		@foreach ($completedGames as $game)
			@include('games.partials.game-card', ['game' => $game])
		@endforeach
	@endif
</div>

</div>

@endsection
