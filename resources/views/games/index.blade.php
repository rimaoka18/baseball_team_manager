@extends('layouts.app')

@section('content')

<!-- App Title -->
<h1 class="text-3xl font-extrabold text-center mb-8">🏅 Blitz Fang スコアボード</h1>
<div class="flex justify-center mb-6">
<a href="{{ route('players.search') }}"
   class="inline-block bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm font-semibold px-4 py-2 rounded-md shadow-sm transition">
    👤 プレイヤー検索
</a>

</div>

<!-- Leaderboard Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <!-- Batting AVG -->
    <div class="bg-white shadow-md rounded-xl p-4">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">🏆 打率ランキング</h2>
        <ul>
            @php
                $medals = ['🥇', '🥈', '🥉'];
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
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">🔥 防御率ランキング</h2>
        <ul>
            @php
                $medals = ['🥇', '🥈', '🥉'];
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


<!-- Header and Add Button -->
<div class="flex items-center justify-between mb-6">
	<h2 class="text-xl font-bold">試合一覧</h2>
	<a href="{{ route('games.create') }}">
		<button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
			＋ 新しいを追加
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
				Blitz Fang <span class="font-bold text-black-700">{{ $game->team_score }}</span>
				-
				<span class="font-bold text-gray-700">{{ $game->opponent_score }}</span> {{ $game->opponent }}
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
