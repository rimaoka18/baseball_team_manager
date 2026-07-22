@php
	$result = $game->result;

	$badgeClass = match ($result) {
		'win' => 'bg-green-100 text-green-700',
		'loss' => 'bg-red-100 text-red-600',
		'tie' => 'bg-gray-200 text-gray-600',
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
	$opponentInitial = $game->opponent ? mb_substr($game->opponent, 0, 1) : '?';
@endphp
<div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-2 mt-3 transition duration-200 hover:-translate-y-[3px] hover:shadow-[0_20px_35px_rgba(0,0,0,0.2)]">
	<div class="flex items-center justify-between">
		<div class="flex items-center gap-2">
			<span class="text-gray-500 text-sm">{{ $game->game_date }}</span>
			@if ($game->game_time_formatted)
				<span class="inline-flex items-center gap-1 text-gray-500 text-xs bg-gray-100 rounded-full px-2 py-0.5">
					<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					{{ $game->game_time_formatted }}
				</span>
			@endif
		</div>
		<span class="inline-block {{ $badgeClass }} rounded-full px-2.5 py-0.5 text-xs font-semibold">
			{{ $badgeLabel }}
		</span>
	</div>

	<div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 mt-2">
		<div class="flex flex-col items-center">
			<img src="{{ asset('images/logo.png') }}" alt="Blitz Fang" class="rounded-full object-cover border border-gray-200" style="width: 96px; height: 96px;">
			<span class="text-sm font-bold text-bf-navy leading-tight mt-1">Blitz Fang</span>
		</div>

		<div class="flex flex-col items-center justify-center text-center px-2">
			@if ($result)
				<div class="text-2xl font-bold text-bf-navy leading-tight">
					{{ $game->team_score }} - {{ $game->opponent_score }}
				</div>
			@else
				<div class="text-base font-semibold text-gray-400 leading-tight">vs</div>
			@endif
			@if ($game->location)
				<div class="text-xs text-gray-500 mt-1">{{ $game->location }}</div>
			@endif
		</div>

		<div class="flex flex-col items-center">
			<div class="rounded-full bg-gray-200 border border-gray-200 flex items-center justify-center" style="width: 96px; height: 96px;">
				<span class="text-3xl font-semibold text-gray-500">{{ $opponentInitial }}</span>
			</div>
			<span class="text-sm font-bold text-bf-navy leading-tight mt-1">{{ $game->opponent ?? '未入力' }}</span>
		</div>
	</div>

	<div class="flex items-center justify-end gap-2 mt-1.5">
		@unless ($hasScore)
			<a href="{{ route('games.upcoming.edit', $game) }}?from=games.index"
				class="border border-bf-navy text-bf-navy bg-bf-cream text-sm px-4 py-1 rounded-lg hover:bg-bf-gold/20 transition">
				予定・スタメンを編集
			</a>
		@endunless

		<a href="{{ $scoreActionRoute }}"
			class="bg-bf-navy text-white text-sm px-4 py-1 rounded-lg hover:bg-bf-navy-light transition">
			{{ $scoreActionLabel }}
		</a>
	</div>
</div>
