@extends('layouts.app')

@section('content')

<div class="space-y-6">

<h2 class="text-lg font-bold">成績</h2>

<div class="bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">

<div class="flex justify-center mb-6">
	<a href="{{ route('players.search') }}"
		class="inline-block bg-bf-navy hover:bg-bf-navy-light text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm transition">
		プレイヤー検索
	</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
	<!-- Batting AVG -->
	<div class="bg-bf-cream shadow-md rounded-xl p-4">
		<h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">打率ランキング</h2>
		<ul>
			@php
				$medals = ['1位', '2位', '3位'];
				$loopIndex = 0;
			@endphp

			@foreach ($topBatters as $entry)
				<li class="flex justify-between items-center py-2 px-2 border-b last:border-none {{ $loopIndex === 0 ? 'border-l-4 border-bf-gold bg-bf-gold/10' : '' }}">
					<span class="font-medium text-gray-800">
						<span class="mr-1">{{ $medals[$loopIndex] ?? '' }}</span>
						{{ $entry['player']->name }}
					</span>
					<span class="text-right font-semibold text-bf-navy">
						{{ number_format($entry['avg'], 3) }}
					</span>
				</li>
				@php $loopIndex++; @endphp
			@endforeach

			@if (count($topBatters) === 0)
				<li class="flex flex-col items-center justify-center text-center py-8 px-4">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor" class="w-9 h-9 text-gray-300 mb-3">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
					</svg>
					<p class="text-gray-500 font-medium">まだ記録がありません</p>
					<p class="text-gray-400 text-sm mt-1">試合結果を入力すると、ここにランキングが表示されます</p>
					<a href="{{ route('games.create') }}" class="mt-3 text-sm text-bf-navy hover:text-bf-gold underline underline-offset-2">試合結果を追加</a>
				</li>
			@endif
		</ul>
	</div>

	<!-- ERA -->
	<div class="bg-bf-cream shadow-md rounded-xl p-4">
		<h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">防御率ランキング</h2>
		<ul>
			@php
				$medals = ['1位', '2位', '3位'];
				$loopIndex = 0;
			@endphp

			@foreach ($topPitchers as $entry)
				<li class="flex justify-between items-center py-2 px-2 border-b last:border-none {{ $loopIndex === 0 ? 'border-l-4 border-bf-gold bg-bf-gold/10' : '' }}">
					<span class="font-medium text-gray-800">
						<span class="mr-1">{{ $medals[$loopIndex] ?? '' }}</span>
						{{ $entry['player']->name }}
					</span>
					<span class="text-right font-semibold text-bf-navy">
						{{ number_format($entry['era'], 2) }}
					</span>
				</li>
				@php $loopIndex++; @endphp
			@endforeach

			@if (count($topPitchers) === 0)
				<li class="flex flex-col items-center justify-center text-center py-8 px-4">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor" class="w-9 h-9 text-gray-300 mb-3">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
					</svg>
					<p class="text-gray-500 font-medium">まだ記録がありません</p>
					<p class="text-gray-400 text-sm mt-1">試合結果を入力すると、ここにランキングが表示されます</p>
					<a href="{{ route('games.create') }}" class="mt-3 text-sm text-bf-navy hover:text-bf-gold underline underline-offset-2">試合結果を追加</a>
				</li>
			@endif
		</ul>
	</div>
</div>

</div>

<div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
	<h2 class="text-lg font-bold text-gray-800 mb-4">全選手成績</h2>

	<div class="overflow-x-auto">
		<table id="player-stats-table" class="min-w-full text-sm">
			<thead class="bg-gray-50 text-gray-600 text-sm font-medium">
				<tr>
					<th class="px-4 py-2 text-left cursor-pointer select-none" data-sort-key="name" onclick="sortPlayerStatsTable('name')">
						選手名 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="px-4 py-2 text-right cursor-pointer select-none" data-sort-key="at-bats" onclick="sortPlayerStatsTable('at-bats')">
						打数 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="px-4 py-2 text-right cursor-pointer select-none" data-sort-key="hits" onclick="sortPlayerStatsTable('hits')">
						安打 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="px-4 py-2 text-right cursor-pointer select-none" data-sort-key="avg" onclick="sortPlayerStatsTable('avg')">
						打率 <span class="sort-indicator text-xs">▼</span>
					</th>
					<th class="px-4 py-2 text-right cursor-pointer select-none" data-sort-key="ip" onclick="sortPlayerStatsTable('ip')">
						投球回 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="px-4 py-2 text-right cursor-pointer select-none" data-sort-key="era" onclick="sortPlayerStatsTable('era')">
						防御率 <span class="sort-indicator text-xs"></span>
					</th>
				</tr>
			</thead>
			<tbody id="player-stats-rows" class="divide-y divide-gray-100 text-gray-800" data-sort-key="avg" data-sort-dir="desc">
				@foreach ($allPlayerStats as $row)
					<tr class="hover:bg-gray-50"
						data-name="{{ $row['player']->name }}"
						data-at-bats="{{ $row['at_bats'] }}"
						data-hits="{{ $row['hits'] }}"
						data-avg="{{ is_null($row['avg']) ? '' : $row['avg'] }}"
						data-ip="{{ $row['innings_pitched'] }}"
						data-era="{{ is_null($row['era']) ? '' : $row['era'] }}">
						<td class="px-4 py-2">{{ $row['player']->name }}</td>
						<td class="px-4 py-2 text-right">{{ $row['at_bats'] }}</td>
						<td class="px-4 py-2 text-right">{{ $row['hits'] }}</td>
						<td class="px-4 py-2 text-right">{{ is_null($row['avg']) ? '-' : ltrim(number_format($row['avg'], 3), '0') }}</td>
						<td class="px-4 py-2 text-right">{{ $row['innings_pitched'] > 0 ? number_format($row['innings_pitched'], 1) : '-' }}</td>
						<td class="px-4 py-2 text-right">{{ is_null($row['era']) ? '-' : number_format($row['era'], 2) }}</td>
					</tr>
				@endforeach

				@if (count($allPlayerStats) === 0)
					<tr>
						<td colspan="6" class="px-4 py-4 text-center text-gray-500">データがありません</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
</div>

</div>

<script>
	function sortPlayerStatsTable(key) {
		const tbody = document.getElementById('player-stats-rows');
		const rows = Array.from(tbody.querySelectorAll('tr'));

		const currentKey = tbody.getAttribute('data-sort-key');
		const currentDir = tbody.getAttribute('data-sort-dir');
		const dir = (currentKey === key && currentDir === 'asc') ? 'desc' : 'asc';

		rows.sort((a, b) => {
			const aVal = a.getAttribute('data-' + key);
			const bVal = b.getAttribute('data-' + key);
			const aEmpty = aVal === '' || aVal === null;
			const bEmpty = bVal === '' || bVal === null;

			if (aEmpty && bEmpty) return 0;
			if (aEmpty) return 1;
			if (bEmpty) return -1;

			const diff = key === 'name'
				? aVal.localeCompare(bVal, 'ja')
				: parseFloat(aVal) - parseFloat(bVal);

			return dir === 'asc' ? diff : -diff;
		});

		rows.forEach(row => tbody.appendChild(row));

		tbody.setAttribute('data-sort-key', key);
		tbody.setAttribute('data-sort-dir', dir);

		document.querySelectorAll('#player-stats-table th[data-sort-key]').forEach(th => {
			const indicator = th.querySelector('.sort-indicator');
			indicator.textContent = th.getAttribute('data-sort-key') === key ? (dir === 'asc' ? '▲' : '▼') : '';
		});
	}
</script>

@endsection
