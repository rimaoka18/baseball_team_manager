@extends('layouts.app')

@section('content')

<div class="max-w-[980px] mx-auto">

<div class="flex items-center justify-between flex-wrap gap-3">
	<h1 class="font-heading text-3xl font-extrabold">選手一覧（{{ $allPlayerStats->count() }}人）</h1>
	<div class="flex items-center gap-2 flex-wrap">
		<a href="{{ route('roster.rankings') }}" class="bf-btn bf-btn-secondary">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
			成績を見る
		</a>
		<a href="{{ route('players.search') }}" class="bf-btn bf-btn-secondary">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
			プレイヤー検索
		</a>
		@auth
		<a href="{{ route('roster.players.create') }}" class="bf-btn bf-btn-primary">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
			選手を追加
		</a>
		@endauth
	</div>
</div>

<hr class="border-t-2 border-bf-divider my-6">

@if (session('success'))
	<div data-auto-dismiss class="bg-bf-cream text-bf-navy border border-bf-divider p-3 mb-6 font-medium">{{ session('success') }}</div>
@endif

@include('partials.validation-errors')

@if ($allPlayerStats->isEmpty())
	<div class="border border-dashed border-bf-divider px-4 py-8 text-center">
		<p class="font-heading font-extrabold mb-1">選手がいません</p>
		@auth
			<p class="text-sm text-bf-ink/60">「選手を追加」から選手を登録してください</p>
		@endauth
	</div>
@else
	<div class="overflow-x-auto">
		<table id="player-stats-table" class="w-full text-sm border-collapse">
			<thead>
				<tr>
					<th class="bf-kicker text-left pb-2 border-b-2 border-bf-divider cursor-pointer select-none w-[70px]" data-sort-key="jersey" onclick="sortPlayerStatsTable('jersey')">
						背番号 <span class="sort-indicator text-xs">▲</span>
					</th>
					<th class="bf-kicker text-left pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="name" onclick="sortPlayerStatsTable('name')">
						選手名 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="bf-kicker text-right pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="at-bats" onclick="sortPlayerStatsTable('at-bats')">
						打数 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="bf-kicker text-right pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="hits" onclick="sortPlayerStatsTable('hits')">
						安打 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="bf-kicker text-right pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="avg" onclick="sortPlayerStatsTable('avg')">
						打率 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="bf-kicker text-right pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="ip" onclick="sortPlayerStatsTable('ip')">
						投球回 <span class="sort-indicator text-xs"></span>
					</th>
					<th class="bf-kicker text-right pb-2 border-b-2 border-bf-divider cursor-pointer select-none" data-sort-key="era" onclick="sortPlayerStatsTable('era')">
						防御率 <span class="sort-indicator text-xs"></span>
					</th>
				</tr>
			</thead>
			<tbody id="player-stats-rows" data-sort-key="jersey" data-sort-dir="asc">
				@foreach ($allPlayerStats as $row)
					<tr class="bf-table-row" onclick="location.href='{{ route('roster.players.show', $row['player']) }}'"
						data-jersey="{{ $row['player']->jersey_number ?? '' }}"
						data-name="{{ $row['player']->name }}"
						data-at-bats="{{ $row['at_bats'] }}"
						data-hits="{{ $row['hits'] }}"
						data-avg="{{ is_null($row['avg']) ? '' : $row['avg'] }}"
						data-ip="{{ $row['innings_pitched'] }}"
						data-era="{{ is_null($row['era']) ? '' : $row['era'] }}">
						<td class="py-2 border-b border-bf-divider font-heading font-extrabold text-bf-navy">{{ is_null($row['player']->jersey_number) ? '-' : $row['player']->jersey_number }}</td>
						<td class="py-2 border-b border-bf-divider">{{ $row['player']->name }}</td>
						<td class="py-2 border-b border-bf-divider text-right">{{ $row['at_bats'] }}</td>
						<td class="py-2 border-b border-bf-divider text-right">{{ $row['hits'] }}</td>
						<td class="py-2 border-b border-bf-divider text-right font-semibold">{{ is_null($row['avg']) ? '-' : ltrim(number_format($row['avg'], 3), '0') }}</td>
						<td class="py-2 border-b border-bf-divider text-right">{{ $row['innings_pitched'] > 0 ? number_format($row['innings_pitched'], 1) : '-' }}</td>
						<td class="py-2 border-b border-bf-divider text-right">{{ is_null($row['era']) ? '-' : number_format($row['era'], 2) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif

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
