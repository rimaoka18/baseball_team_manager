@extends('layouts.app')

@section('content')

<div class="bg-bf-bg text-bf-ink p-6 md:p-8">
<div class="max-w-2xl mx-auto">

	<div class="flex flex-wrap items-end justify-between gap-4 mb-4">
		<h1 class="font-heading text-3xl md:text-[42px] font-extrabold leading-none">スケジュール</h1>
		@auth
		<a href="{{ route('games.upcoming.create') }}" class="bf-btn bf-btn-primary">
			試合を追加
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
		</a>
		@endauth
	</div>

	<hr class="border-t-2 border-bf-divider">

	@if ($upcomingGames->isEmpty())
		<div class="border border-bf-divider px-4 py-8 mt-6 text-center">
			<p class="font-heading font-extrabold text-lg mb-1">予定されている試合はありません</p>
			<p class="text-sm text-bf-ink/60 mb-4">試合を追加してスタメンを登録しましょう</p>
			@auth
			<a href="{{ route('games.upcoming.create') }}" class="bf-btn bf-btn-primary">
				試合を追加
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
			</a>
			@endauth
		</div>
	@else
		<div class="flex gap-6 overflow-x-auto my-6">
			@foreach ($upcomingGames as $index => $game)
				@php
					$date = \Illuminate\Support\Carbon::parse($game->game_date);
					$isActive = $index === 0;
				@endphp
				<button type="button"
					id="upcoming-tab-{{ $index }}"
					onclick="showUpcomingGame({{ $index }})"
					class="upcoming-tab-btn bf-tab {{ $isActive ? 'bf-tab-active' : '' }}">
					<span class="block text-[10px] tracking-[0.08em] uppercase opacity-60">{{ $date->format('n/j') }}</span>
					<span class="block text-[15px] font-heading font-extrabold whitespace-nowrap">{{ $game->opponent ?? '未定' }}</span>
				</button>
			@endforeach
		</div>

		@foreach ($upcomingGames as $index => $game)
			<div id="upcoming-panel-{{ $index }}" class="upcoming-game-panel {{ $index === 0 ? '' : 'hidden' }}">
				@php
					$hasScore = !is_null($game->team_score) && !is_null($game->opponent_score);
					$scoreActionLabel = $hasScore ? '試合結果' : '試合結果を入力';
					$scoreActionRoute = $hasScore ? route('games.show', $game) : route('games.edit', $game);
				@endphp

				<div class="flex flex-wrap items-center gap-3 mb-2 text-[13px] text-bf-ink/60">
					@if ($game->game_time_formatted)
						<span class="inline-flex items-center gap-1.5">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
							{{ $game->game_time_formatted }}
						</span>
					@endif
					@if ($game->location)
						<span class="inline-flex items-center gap-1.5">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
							{{ $game->location }}
						</span>
					@endif
				</div>

				<h2 class="font-heading text-2xl md:text-[32px] font-extrabold mb-4">
					Blitz Fang <span class="font-sans font-normal text-bf-ink/50">vs</span> {{ $game->opponent ?? '未定' }}
				</h2>

				<div class="flex flex-wrap gap-2 mb-6">
					@unless ($hasScore)
						@auth
							<a href="{{ route('games.upcoming.edit', $game) }}" class="bf-btn bf-btn-secondary">
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

				<hr class="border-t-2 border-bf-divider">

				<div class="mt-6">
					<h4 class="font-heading text-xl font-extrabold mb-3">スタメン</h4>

					@if ($game->lineups->isEmpty())
						<div class="border border-bf-divider px-4 py-8 text-center">
							<p class="font-heading font-extrabold text-[17px] mb-1">スタメン未登録</p>
							<p class="text-sm text-bf-ink/60 mb-4">打順と守備位置を登録するとここに表示されます</p>
							@unless ($hasScore)
								@auth
									<a href="{{ route('games.upcoming.edit', $game) }}" class="bf-btn bf-btn-primary">
										スタメンを登録する
									</a>
								@endauth
							@endunless
						</div>
					@else
						<p class="text-xs text-bf-ink/50 mb-2 md:hidden">表は横にスクロールできます</p>
						<div class="overflow-x-auto">
							<table class="w-full min-w-[420px] text-sm">
								<thead>
									<tr class="border-b-2 border-bf-divider">
										<th class="w-[60px] text-left text-[11px] uppercase tracking-[0.08em] opacity-60 font-semibold pb-2">打順</th>
										<th class="w-[60px] text-left text-[11px] uppercase tracking-[0.08em] opacity-60 font-semibold pb-2">守備</th>
										<th class="text-left text-[11px] uppercase tracking-[0.08em] opacity-60 font-semibold pb-2">選手名</th>
										<th class="w-[70px] text-right text-[11px] uppercase tracking-[0.08em] opacity-60 font-semibold pb-2">背番号</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($game->lineups as $lineup)
										<tr class="border-b border-bf-divider hover:bg-black/5">
											<td class="py-2.5">{{ $lineup->batting_order }}</td>
											<td class="py-2.5">{{ $lineup->position }}</td>
											<td class="py-2.5 font-semibold">{{ $lineup->player->name }}</td>
											<td class="py-2.5 text-right text-bf-ink/60">
												{{ !is_null($lineup->player->jersey_number) ? '#'.$lineup->player->jersey_number : '-' }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
			</div>
		@endforeach
	@endif

</div>
</div>

<script>
	function showUpcomingGame(index) {
		document.querySelectorAll('.upcoming-game-panel').forEach(panel => panel.classList.add('hidden'));
		document.getElementById('upcoming-panel-' + index).classList.remove('hidden');

		document.querySelectorAll('.upcoming-tab-btn').forEach(btn => btn.classList.remove('bf-tab-active'));
		document.getElementById('upcoming-tab-' + index).classList.add('bf-tab-active');
	}
</script>

@endsection
