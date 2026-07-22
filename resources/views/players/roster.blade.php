@extends('layouts.app')

@section('content')

<div class="space-y-6">

<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold text-bf-cream">選手</h2>
	<a href="{{ route('players.search') }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full transition">
		プレイヤー検索
	</a>
</div>

@if (session('success'))
	<div data-auto-dismiss class="bg-bf-cream text-bf-navy border border-bf-gold/50 p-3 rounded-xl font-medium">{{ session('success') }}</div>
@endif

@include('partials.validation-errors')

<div class="bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	<h3 class="text-lg font-semibold text-bf-navy mb-4">選手を追加</h3>
	<form method="POST" action="{{ route('roster.players.store') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
		@csrf
		<div class="flex-1">
			<label class="block text-sm font-medium text-bf-navy mb-1">選手名</label>
			<input type="text" name="name" value="{{ old('name') }}" required
				placeholder="例：山田"
				class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
		</div>
		<button type="submit"
			class="bg-bf-navy text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-bf-navy-light transition">
			追加する
		</button>
	</form>
	<p class="text-sm text-gray-500 mt-3">ここに登録した選手は、スケジュールのスタメン選択で選べます。</p>
</div>

<div class="bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	<h3 class="text-lg font-bold text-gray-800 mb-4">成績ランキング</h3>

	<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
		<div class="bg-white/50 shadow-sm rounded-xl p-4 border border-gray-100">
			<h4 class="text-base font-bold text-gray-800 border-b pb-2 mb-4">打率ランキング</h4>
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
					<li class="py-6 text-center text-gray-500 text-sm">まだ記録がありません</li>
				@endif
			</ul>
		</div>

		<div class="bg-white/50 shadow-sm rounded-xl p-4 border border-gray-100">
			<h4 class="text-base font-bold text-gray-800 border-b pb-2 mb-4">防御率ランキング</h4>
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
					<li class="py-6 text-center text-gray-500 text-sm">まだ記録がありません</li>
				@endif
			</ul>
		</div>
	</div>
</div>

<div class="bg-bf-cream rounded-xl border border-gray-200 shadow-sm p-6">
	<div class="flex items-center justify-between mb-4">
		<h3 class="text-lg font-bold text-gray-800">選手一覧（{{ $allPlayerStats->count() }}人）</h3>
	</div>

	@if ($allPlayerStats->isEmpty())
		<div class="rounded-xl border border-dashed border-gray-300 bg-white/50 px-4 py-8 text-center">
			<p class="text-gray-800 font-semibold mb-1">選手がいません</p>
			<p class="text-sm text-gray-500">上のフォームから選手を追加してください</p>
		</div>
	@else
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
				</tbody>
			</table>
		</div>
	@endif
</div>

</div>

<script>
	function sortPlayerStatsTable(key) {
		const tbody = document.getElementById('player-stats-rows');
		if (!tbody) return;

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
