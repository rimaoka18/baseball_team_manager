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

		$detailRoute = $result
			? route('games.show', $game)
			: route('games.upcoming.edit', $game);
	@endphp
	<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 cursor-pointer hover:shadow-md hover:border-bf-navy/30 transition"
		onclick="window.location='{{ $detailRoute }}'">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-2">
				<span class="text-gray-500 text-sm">{{ $game->game_date }}</span>
				<span class="text-gray-700 text-sm">{{ $game->opponent ?? '未入力' }}</span>
			</div>
			<span class="inline-block {{ $badgeClass }} rounded-full px-3 py-1 text-xs font-semibold">
				{{ $badgeLabel }}
			</span>
		</div>

		<div class="text-center mt-3">
			@if ($result)
				<div class="text-3xl font-bold text-bf-navy">
					{{ $game->team_score }} - {{ $game->opponent_score }}
				</div>
				<div class="text-gray-400 text-xs mt-1 h-4">vs {{ $game->opponent }}</div>
			@else
				<div class="text-2xl font-semibold text-gray-300">- vs -</div>
				<div class="text-gray-400 text-xs mt-1 h-4">{{ $game->location }}</div>
			@endif
		</div>

		<div class="flex justify-end mt-3">
			<form action="{{ route('games.destroy', $game) }}" method="POST"
				onclick="event.stopPropagation()"
				onsubmit="return confirm('本当に削除しますか？');">
				@csrf
				@method('DELETE')
				<button type="submit" aria-label="削除"
					class="text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full p-1.5 transition">
					<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3m3 0-.867 12.142A2 2 0 0 1 15.138 21H8.862a2 2 0 0 1-1.995-1.858L6 7h12Z" />
					</svg>
				</button>
			</form>
		</div>
	</div>
	@endforeach
</div>

</div>

@endsection
