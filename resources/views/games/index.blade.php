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

<!-- Games Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
<table class="min-w-full bg-white border shadow-sm rounded text-sm">
	<thead class="bg-gray-100 text-gray-600 uppercase tracking-wider">
		<tr>
			<th class="text-left px-4 py-2 border">日付</th>
			<th class="text-left px-4 py-2 border">相手チーム</th>
			<th class="text-left px-4 py-2 border">スコア</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($games as $game)
		@php
			$result = null;
			if (!is_null($game->team_score) && !is_null($game->opponent_score)) {
				$result = $game->team_score > $game->opponent_score
					? 'win'
					: ($game->team_score < $game->opponent_score ? 'loss' : 'tie');
			}

			$rowBorderClass = match ($result) {
				'win' => 'border-green-500',
				'loss' => 'border-red-400',
				'tie' => 'border-bf-gold',
				default => 'border-gray-200',
			};

			$scoreTextClass = match ($result) {
				'win' => 'text-green-700 font-semibold',
				'loss' => 'text-red-600 font-semibold',
				'tie' => 'text-bf-navy font-semibold',
				default => '',
			};
		@endphp
		<tr class="border-t border-l-4 {{ $rowBorderClass }} hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('games.show', $game) }}'">
			<td class="px-4 py-2 border">{{ $game->game_date }}</td>
			<td class="px-4 py-2 border">{{ $game->opponent ?? '未入力' }}</td>
			<td class="px-4 py-2 border font-medium text-sm">
				@if (is_null($game->team_score) || is_null($game->opponent_score))
					@if ($game->game_date < now()->toDateString())
						<span class="inline-block bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded-full">
							スコア未入力
						</span>
					@else
						<span class="text-gray-400">-</span>
					@endif
				@else
					<span class="{{ $scoreTextClass }}">
						Blitz Fang {{ $game->team_score }} - {{ $game->opponent_score }} {{ $game->opponent }}
					</span>
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
</div>

</div>

@endsection
