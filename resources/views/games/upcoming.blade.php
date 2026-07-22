@extends('layouts.app')

@section('content')

<div class="space-y-6">

<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold text-bf-cream">スケジュール</h2>
	<a href="{{ route('games.upcoming.create') }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full transition">
		＋ 試合を追加
	</a>
</div>

<div class="max-w-2xl mx-auto bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	@if ($upcomingGames->isEmpty())
		<div class="text-center py-8">
			<p class="text-gray-600 font-medium mb-1">予定されている試合はありません</p>
			<p class="text-sm text-gray-500 mb-4">試合を追加してスタメンを登録しましょう</p>
			<a href="{{ route('games.upcoming.create') }}"
				class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-2 rounded-full hover:bg-bf-navy-light transition">
				＋ 試合を追加
			</a>
		</div>
	@else
		@if ($upcomingGames->count() > 1)
			@php
				$monthLabel = \Illuminate\Support\Carbon::parse($upcomingGames->first()->game_date)->translatedFormat('M');
			@endphp
			<div class="flex items-stretch justify-center gap-2 mb-5 overflow-x-auto pb-1">
				<span class="shrink-0 self-center text-xs font-bold text-gray-400 uppercase tracking-wide pr-1">{{ $monthLabel }}</span>

				@foreach ($upcomingGames as $index => $game)
					@php
						$date = \Illuminate\Support\Carbon::parse($game->game_date);
						$isActive = $index === 0;
					@endphp
					<button type="button"
						id="upcoming-tab-{{ $index }}"
						onclick="showUpcomingGame({{ $index }})"
						style="width: 4rem; min-width: 4rem; max-width: 4rem; height: 4rem;"
						class="upcoming-tab-btn shrink-0 flex flex-col items-center justify-center gap-0.5 overflow-hidden rounded-xl border px-1 py-1.5 transition {{ $isActive ? 'bg-bf-navy border-bf-navy text-white shadow-md' : 'bg-white border-gray-200 text-gray-500 shadow-sm hover:border-bf-navy/40 hover:text-bf-navy' }}">
						<span class="w-full text-center text-[10px] font-semibold leading-none opacity-60">{{ $date->format('n/j') }}</span>
						<span class="w-full text-center text-xs font-bold leading-tight truncate">{{ $game->opponent ?? '未定' }}</span>
						<span class="w-full text-center text-[10px] leading-none opacity-60 truncate">
							{{ $game->game_time_formatted ?? '未定' }}
						</span>
					</button>
				@endforeach
			</div>
		@endif

		@foreach ($upcomingGames as $index => $game)
			<div id="upcoming-panel-{{ $index }}" class="upcoming-game-panel {{ $index === 0 ? '' : 'hidden' }}">
				@php
					$hasScore = !is_null($game->team_score) && !is_null($game->opponent_score);
					$scoreActionLabel = $hasScore ? '試合結果' : '試合結果を入力';
					$scoreActionRoute = $hasScore ? route('games.show', $game) : route('games.edit', $game);
					$opponentInitial = $game->opponent ? mb_substr($game->opponent, 0, 1) : '?';
				@endphp

				<div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
					<div class="flex flex-col items-center">
						<img src="{{ asset('images/logo.png') }}" alt="Blitz Fang" class="rounded-full object-cover border border-gray-200" style="width: 64px; height: 64px;">
						<span class="text-xs font-bold text-bf-navy leading-tight mt-1">Blitz Fang</span>
					</div>

					<div class="flex flex-col items-center justify-center gap-1 px-2">
						<span class="text-sm font-semibold text-gray-400">vs</span>
						<span class="flex items-center gap-1.5 text-xs text-gray-500 whitespace-nowrap">
							@if ($game->game_time_formatted)
								<span>{{ $game->game_time_formatted }}</span>
								<span class="text-gray-300">・</span>
							@endif
							<span>{{ $game->location }}</span>
						</span>
					</div>

					<div class="flex flex-col items-center">
						<div class="rounded-full bg-gray-200 border border-gray-200 flex items-center justify-center" style="width: 64px; height: 64px;">
							<span class="text-xl font-semibold text-gray-500">{{ $opponentInitial }}</span>
						</div>
						<span class="text-xs font-bold text-bf-navy leading-tight mt-1">{{ $game->opponent ?? '未定' }}</span>
					</div>
				</div>

				<div class="flex items-center justify-center gap-2 mt-4">
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

				<div class="border-t border-bf-navy/15 my-4"></div>

				@if ($game->lineups->isEmpty())
					<div class="rounded-xl border border-dashed border-gray-300 bg-black/5 px-4 py-8 text-center">
						<p class="text-gray-800 font-semibold mb-1">スタメン未登録</p>
						<p class="text-sm text-gray-500 mb-4">打順と守備位置を登録するとここに表示されます</p>
						@unless ($hasScore)
							<a href="{{ route('games.upcoming.edit', $game) }}"
								class="inline-block bg-bf-navy text-white text-sm font-semibold px-4 py-2 rounded-full hover:bg-bf-navy-light transition">
								スタメンを登録する
							</a>
						@endunless
					</div>
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
			btn.classList.remove('bg-bf-navy', 'border-bf-navy', 'text-white', 'shadow-sm');
			btn.classList.add('bg-white', 'border-gray-200', 'text-gray-500');
		});
		const activeBtn = document.getElementById('upcoming-tab-' + index);
		activeBtn.classList.remove('bg-white', 'border-gray-200', 'text-gray-500');
		activeBtn.classList.add('bg-bf-navy', 'border-bf-navy', 'text-white', 'shadow-sm');
	}
</script>

@endsection
