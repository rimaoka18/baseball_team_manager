@extends('layouts.app')

@section('content')

<!-- Header and Add Button -->
<div class="flex items-center justify-between mb-6">
	<h2 class="text-xl font-bold">試合一覧</h2>
	<a href="{{ route('games.create') }}">
		<button class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
			＋ 試合結果を追加
		</button>
	</a>
</div>

<!-- Games Table -->
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
		<tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('games.show', $game) }}'">
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
					Blitz Fang <span class="font-bold text-black-700">{{ $game->team_score }}</span>
					-
					<span class="font-bold text-gray-700">{{ $game->opponent_score }}</span> {{ $game->opponent }}
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

@endsection
