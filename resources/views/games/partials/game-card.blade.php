@php
	$result = $game->result;

	$badgeClass = match ($result) {
		'win' => 'bg-green-100 text-green-700',
		'loss' => 'bg-red-100 text-red-600',
		'tie' => 'bg-gray-200 text-gray-600',
		default => 'bg-bf-accent-100 text-bf-accent-400',
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
<div class="bg-bf-surface border border-bf-divider text-bf-ink p-4 mt-3">
	<div class="flex items-center justify-between mb-2">
		<div class="flex items-center gap-3 text-xs text-bf-ink/60">
			<span>{{ $game->game_date }}</span>
			@if ($game->game_time_formatted)
				<span class="inline-flex items-center gap-1">
					<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
					{{ $game->game_time_formatted }}
				</span>
			@endif
		</div>
		<span class="inline-block {{ $badgeClass }} px-2 py-0.5 text-xs font-semibold uppercase tracking-wide">
			{{ $badgeLabel }}
		</span>
	</div>

	<h3 class="font-heading text-lg font-extrabold mb-1">
		Blitz Fang <span class="font-sans font-normal text-bf-ink/50">vs</span> {{ $game->opponent ?? '未入力' }}
	</h3>

	@if ($result)
		<div class="font-heading text-2xl font-extrabold mb-1">
			{{ $game->team_score }} - {{ $game->opponent_score }}
		</div>
	@endif

	@if ($game->location)
		<div class="text-xs text-bf-ink/60">{{ $game->location }}</div>
	@endif

	<div class="flex items-center justify-end gap-2 mt-3">
		@unless ($hasScore)
			@auth
				<a href="{{ route('games.upcoming.edit', $game) }}?from=games.index" class="bf-btn bf-btn-secondary">
					予定・スタメンを編集
				</a>
			@endauth
		@endunless

		@if ($hasScore || auth()->check())
			<a href="{{ $scoreActionRoute }}" class="bf-btn bf-btn-primary">
				{{ $scoreActionLabel }}
			</a>
		@endif
	</div>
</div>
