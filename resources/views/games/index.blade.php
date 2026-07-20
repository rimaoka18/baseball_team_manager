@extends('layouts.app')

@section('content')

<!-- App Title -->
<h1 class="text-3xl font-extrabold text-center mb-8">Blitz Fang スコアボード</h1>
<div class="flex justify-center mb-6">
<a href="{{ route('players.search') }}"
   class="inline-block bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm font-semibold px-4 py-2 rounded-md shadow-sm transition">
    プレイヤー検索
</a>

</div>

<!-- Leaderboard Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <!-- Batting AVG -->
    <div class="bg-white shadow-md rounded-xl p-4">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">打率ランキング</h2>
        <ul>
            @php
                $medals = ['1位', '2位', '3位'];
                $loopIndex = 0;
            @endphp

            @foreach ($topBatters as $entry)
                <li class="flex justify-between py-2 border-b last:border-none">
                    <span class="font-medium">
                        <span class="mr-1">{{ $medals[$loopIndex] ?? '' }}</span>
                        {{ $entry['player']->name }}
                    </span>
                    <span class="text-right font-semibold text-blue-700">
                        {{ number_format($entry['avg'], 3) }}
                    </span>
                </li>
                @php $loopIndex++; @endphp
            @endforeach

            @if (count($topBatters) === 0)
                <li class="text-gray-500">データがありません</li>
            @endif
        </ul>
    </div>

    <!-- ERA -->
    <div class="bg-white shadow-md rounded-xl p-4">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">防御率ランキング</h2>
        <ul>
            @php
                $medals = ['1位', '2位', '3位'];
                $loopIndex = 0;
            @endphp

            @foreach ($topPitchers as $entry)
                <li class="flex justify-between py-2 border-b last:border-none">
                    <span class="font-medium">
                        <span class="mr-1">{{ $medals[$loopIndex] ?? '' }}</span>
                        {{ $entry['player']->name }}
                    </span>
                    <span class="text-right font-semibold text-green-700">
                        {{ number_format($entry['era'], 2) }}
                    </span>
                </li>
                @php $loopIndex++; @endphp
            @endforeach

            @if (count($topPitchers) === 0)
                <li class="text-gray-500">データがありません</li>
            @endif
        </ul>
    </div>
</div>


<!-- Upcoming Game -->
<div class="bg-white shadow-md rounded-xl p-4 mb-10">
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
						class="upcoming-tab-btn px-3 py-1 rounded text-sm font-medium transition {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
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
						<a href="{{ route('games.edit', $game) }}"
							class="bg-yellow-500 text-white text-sm px-3 py-1 rounded hover:bg-yellow-600">
							編集
						</a>
						<form action="{{ route('games.destroy', $game) }}" method="POST"
							onsubmit="return confirm('この試合の予定をキャンセルしますか？');">
							@csrf
							@method('DELETE')
							<button type="submit" class="bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600">
								キャンセル
							</button>
						</form>
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

<!-- Header and Add Button -->
<div class="flex items-center justify-between mb-6">
	<h2 class="text-xl font-bold">試合一覧</h2>
	<a href="{{ route('games.create') }}">
		<button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
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
			<th class="text-left px-4 py-2 border">詳細</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($games as $game)
		<tr class="border-t hover:bg-gray-50">
			<td class="px-4 py-2 border">{{ $game->game_date }}</td>
			<td class="px-4 py-2 border">{{ $game->opponent ?? '未入力' }}</td>
			<td class="px-4 py-2 border font-medium text-sm">
				@if (is_null($game->team_score) || is_null($game->opponent_score))
					@if ($game->game_date < now()->toDateString())
						<span class="inline-block bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded">
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
			<td class="px-4 py-2 border">
				<div class="flex flex-wrap sm:flex-nowrap gap-3">
					<a href="{{ route('games.show', $game) }}"
						class="bg-blue-500 text-white text-sm px-3 py-1 rounded hover:bg-blue-600 text-center">
						試合結果
					</a>
					<a href="{{ route('games.edit', $game) }}"
						class="bg-yellow-500 text-white text-sm px-3 py-1 rounded hover:bg-yellow-600 text-center">
						編集
					</a>
					<form action="{{ route('games.destroy', $game) }}" method="POST"
						onsubmit="return confirm('本当に削除しますか？');">
						@csrf
						@method('DELETE')
						<button type="submit"
							class="bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600 text-center">
							削除
						</button>
					</form>
				</div>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

@endsection
