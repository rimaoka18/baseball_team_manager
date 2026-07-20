@extends('layouts.app')

@section('content')

<div class="bg-white shadow-md rounded-xl p-4">
	<div class="flex items-center justify-between border-b pb-2 mb-4">
		<h2 class="text-lg font-bold text-gray-800">次の試合</h2>
		<a href="{{ route('games.upcoming.create') }}"
			class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded-full transition">
			＋ 次の試合を作成
		</a>
	</div>

	@if ($upcomingGames->isEmpty())
		<p class="text-gray-500">予定されている試合はありません</p>
	@else
		@if ($upcomingGames->count() > 1)
			<div class="flex flex-wrap gap-2 mb-4">
				@foreach ($upcomingGames as $index => $game)
					<button type="button"
						id="upcoming-tab-{{ $index }}"
						onclick="showUpcomingGame({{ $index }})"
						class="upcoming-tab-btn px-3 py-1 rounded-full text-sm font-medium transition {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
						{{ \Illuminate\Support\Carbon::parse($game->game_date)->format('n/j') }}
					</button>
				@endforeach
			</div>
		@endif

		@foreach ($upcomingGames as $index => $game)
			<div id="upcoming-panel-{{ $index }}" class="upcoming-game-panel {{ $index === 0 ? '' : 'hidden' }}">
				<div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 mb-4">
					<div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
						<span class="text-xl font-semibold">{{ \Illuminate\Support\Carbon::parse($game->game_date)->format('Y/m/d') }}</span>
						<span class="text-gray-600">vs {{ $game->opponent ?? '未定' }}</span>
						<span class="text-gray-500 text-sm">@ {{ $game->location }}</span>
					</div>
					<div class="flex gap-2">
						<a href="{{ route('games.upcoming.edit', $game) }}"
							class="bg-yellow-500 text-white text-sm px-3 py-1 rounded-full hover:bg-yellow-600">
							編集
						</a>
					</div>
				</div>

				@if ($game->lineups->isEmpty())
					<p class="text-gray-500">スターティングラインナップは未登録です</p>
				@else
					<ul id="upcoming-lineup-preview-{{ $index }}">
						@foreach ($game->lineups as $lineup)
							<li class="flex justify-between py-1 border-b last:border-none {{ $loop->index >= 9 ? 'hidden upcoming-lineup-extra-' . $index : '' }}">
								<span>
									<span class="inline-block w-6 text-gray-500 text-sm">{{ $lineup->batting_order }}</span>
									{{ $lineup->player->name }}
								</span>
								<span class="text-gray-500 text-sm">{{ $lineup->position }}</span>
							</li>
						@endforeach
					</ul>

					@if ($game->lineups->count() > 9)
						<button type="button" onclick="toggleLineupPreview(this, {{ $index }})" class="mt-3 text-blue-600 hover:underline text-sm">
							もっと見る
						</button>
					@endif
				@endif
			</div>
		@endforeach
	@endif
</div>

<script>
	function showUpcomingGame(index) {
		document.querySelectorAll('.upcoming-game-panel').forEach(panel => panel.classList.add('hidden'));
		document.getElementById('upcoming-panel-' + index).classList.remove('hidden');

		document.querySelectorAll('.upcoming-tab-btn').forEach(btn => {
			btn.classList.remove('bg-blue-600', 'text-white');
			btn.classList.add('bg-gray-100', 'text-gray-700');
		});
		const activeBtn = document.getElementById('upcoming-tab-' + index);
		activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
		activeBtn.classList.add('bg-blue-600', 'text-white');
	}

	function toggleLineupPreview(button, index) {
		const hidden = document.querySelectorAll('.upcoming-lineup-extra-' + index);
		const expand = hidden.length > 0 && hidden[0].classList.contains('hidden');
		hidden.forEach(row => row.classList.toggle('hidden', !expand));
		button.textContent = expand ? '閉じる' : 'もっと見る';
	}
</script>

@endsection
