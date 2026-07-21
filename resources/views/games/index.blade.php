@extends('layouts.app')

@section('content')

<div class="space-y-6">

<!-- Header and Add Button -->
<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold">試合一覧</h2>
	<a href="{{ route('games.create') }}">
		<button class="bg-bf-navy text-white px-4 py-2 rounded-lg hover:bg-bf-navy-light transition">
			＋ 試合結果を追加
		</button>
	</a>
</div>

<!-- Games List -->
<div class="max-w-2xl mx-auto space-y-3">
	@foreach ($games as $game)
	@php
		$result = null;
		if (!is_null($game->team_score) && !is_null($game->opponent_score)) {
			$result = $game->team_score > $game->opponent_score
				? 'win'
				: ($game->team_score < $game->opponent_score ? 'loss' : 'tie');
		}

		$badgeClass = match ($result) {
			'win' => 'bg-green-100 text-green-700',
			'loss' => 'bg-red-100 text-red-600',
			'tie' => 'bg-bf-gold/20 text-bf-navy',
			default => 'bg-gray-100 text-gray-500',
		};

		$badgeLabel = match ($result) {
			'win' => '勝',
			'loss' => '負',
			'tie' => '分',
			default => '予定',
		};

		$hasScore = (bool) $result;
		$scoreActionLabel = $hasScore ? '試合結果' : '試合結果を入力';
		$scoreActionRoute = $hasScore ? route('games.show', $game) : route('games.edit', $game);
	@endphp
	<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-2">
				<span class="text-gray-500 text-sm">{{ $game->game_date }}</span>
				<span class="text-gray-700 text-sm">{{ $game->opponent ?? '未入力' }}</span>
			</div>
			<span class="inline-block {{ $badgeClass }} rounded-full px-3 py-1 text-xs font-semibold">
				{{ $badgeLabel }}
			</span>
		</div>

		<div class="relative mt-3">
			<img src="{{ asset('images/logo.png') }}" alt="Blitz Fang" class="absolute left-1/4 top-1/2 -translate-y-1/2 w-20 h-20 rounded-full object-cover border border-gray-200">
			<div class="text-center">
				@if ($result)
					<div class="text-3xl font-bold text-bf-navy leading-tight">
						{{ $game->team_score }} - {{ $game->opponent_score }}
					</div>
					<div class="text-gray-400 text-xs mt-1 h-5">vs {{ $game->opponent }}</div>
				@else
					<div class="text-2xl font-semibold text-gray-300 leading-tight">- vs -</div>
					<div class="text-gray-400 text-xs mt-1 h-5">{{ $game->location }}</div>
				@endif
			</div>
		</div>

		<div class="flex items-center justify-end gap-2 mt-3">
			@unless ($hasScore)
				<a href="{{ route('games.upcoming.edit', $game) }}"
					class="border border-bf-navy text-bf-navy bg-white text-sm px-4 py-1.5 rounded-lg hover:bg-bf-cream transition">
					予定・スタメンを編集
				</a>
			@endunless

			<a href="{{ $scoreActionRoute }}"
				class="bg-bf-navy text-white text-sm px-4 py-1.5 rounded-lg hover:bg-bf-navy-light transition">
				{{ $scoreActionLabel }}
			</a>
		</div>
	</div>
	@endforeach
</div>

</div>

@endsection
