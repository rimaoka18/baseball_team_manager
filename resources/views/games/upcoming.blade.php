@extends('layouts.app')

@section('content')

<div class="space-y-6">

<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold text-gray-800">次の試合</h2>
	<a href="{{ route('games.upcoming.create') }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full transition">
		＋ 次の試合を作成
	</a>
</div>

<div class="max-w-2xl mx-auto bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	@if ($upcomingGames->isEmpty())
		<p class="text-gray-500">予定されている試合はありません</p>
	@else
		@if ($upcomingGames->count() > 1)
			<div class="flex flex-wrap gap-2 mb-4">
				@foreach ($upcomingGames as $index => $game)
					<button type="button"
						id="upcoming-tab-{{ $index }}"
						onclick="showUpcomingGame({{ $index }})"
						class="upcoming-tab-btn px-3 py-1 rounded-full text-sm font-medium transition {{ $index === 0 ? 'bg-bf-navy text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
						{{ \Illuminate\Support\Carbon::parse($game->game_date)->format('n/j') }}
					</button>
				@endforeach
			</div>
		@endif

		@foreach ($upcomingGames as $index => $game)
			<div id="upcoming-panel-{{ $index }}" class="upcoming-game-panel {{ $index === 0 ? '' : 'hidden' }}">
				<div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 mb-3">
					<div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
						<span class="text-xl font-semibold text-gray-800">{{ \Illuminate\Support\Carbon::parse($game->game_date)->format('Y/m/d') }}</span>
						<span class="text-gray-600">vs {{ $game->opponent ?? '未定' }}</span>
						<span class="text-gray-500 text-sm">@ {{ $game->location }}</span>
					</div>
					@php
						$hasScore = !is_null($game->team_score) && !is_null($game->opponent_score);
						$scoreActionLabel = $hasScore ? '試合結果' : '試合結果を入力';
						$scoreActionRoute = $hasScore ? route('games.show', $game) : route('games.edit', $game);
					@endphp
					<div class="flex gap-2">
						@unless ($hasScore)
							<a href="{{ route('games.upcoming.edit', $game) }}"
								class="border border-bf-navy text-bf-navy bg-bf-cream text-sm px-3 py-1 rounded-lg hover:bg-bf-gold/20">
								予定・スタメンを編集
							</a>
						@endunless
						<a href="{{ $scoreActionRoute }}"
							class="bg-bf-navy text-white text-sm px-3 py-1 rounded-lg hover:bg-bf-navy-light">
							{{ $scoreActionLabel }}
						</a>
					</div>
				</div>

				@if ($game->lineups->isEmpty())
					<p class="text-gray-500">スターティングラインナップは未登録です</p>
				@else
					<div class="grid grid-cols-[2rem_minmax(0,1fr)_3.25rem_5.5rem] items-center gap-x-2 px-1 pb-2 text-sm text-gray-500 font-semibold tracking-wide">
						<span class="text-center">打順</span>
						<span>選手名</span>
						<span class="text-center">守備</span>
						<span class="text-right">成績</span>
					</div>

					<ul id="upcoming-lineup-preview-{{ $index }}" class="divide-y divide-gray-100">
						@foreach ($game->lineups as $lineup)
							@php
								$isPitcher = $lineup->position === 'P';
								$statValue = $isPitcher
									? $playerStatService->getERAForPlayer($lineup->player)
									: $playerStatService->getBattingAverageForPlayer($lineup->player);
								$statLabel = $isPitcher ? '防御率' : '打率';
								$statText = is_null($statValue)
									? null
									: ($isPitcher ? number_format($statValue, 2) : ltrim(number_format($statValue, 3), '0'));
								$pillClass = match ($lineup->position) {
									'P', 'C' => 'bg-bf-navy/10 text-bf-navy',
									'1B', '2B', '3B', 'SS' => 'bg-bf-gold/15 text-bf-navy',
									'LF', 'CF', 'RF' => 'bg-gray-100 text-gray-600',
									default => 'bg-gray-100 text-gray-600',
								};
							@endphp
							<li class="grid grid-cols-[2rem_minmax(0,1fr)_3.25rem_5.5rem] items-center gap-x-2 py-2.5 px-1 odd:bg-gray-50/50">
								<span class="w-8 h-8 rounded-full bg-bf-navy text-white flex items-center justify-center text-sm font-semibold">
									{{ $lineup->batting_order }}
								</span>
								<span class="text-gray-800 font-medium truncate">{{ $lineup->player->name }}</span>
								<div class="flex justify-center">
									@if ($lineup->position)
										<span class="{{ $pillClass }} text-xs font-medium rounded-full w-10 text-center py-1">
											{{ $lineup->position }}
										</span>
									@endif
								</div>
								<div class="text-right">
									@if ($statText)
										<span class="text-gray-600 text-sm">
											{{ $statLabel }} <span class="font-medium text-gray-800">{{ $statText }}</span>
										</span>
									@else
										<span class="text-xs text-gray-500">{{ $statLabel }} 未記録</span>
									@endif
								</div>
							</li>
						@endforeach
					</ul>
				@endif
			</div>
		@endforeach
	@endif
</div>

</div>

<script>
	function showUpcomingGame(index) {
		document.querySelectorAll('.upcoming-game-panel').forEach(panel => panel.classList.add('hidden'));
		document.getElementById('upcoming-panel-' + index).classList.remove('hidden');

		document.querySelectorAll('.upcoming-tab-btn').forEach(btn => {
			btn.classList.remove('bg-bf-navy', 'text-white');
			btn.classList.add('bg-gray-100', 'text-gray-700');
		});
		const activeBtn = document.getElementById('upcoming-tab-' + index);
		activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
		activeBtn.classList.add('bg-bf-navy', 'text-white');
	}
</script>

@endsection
