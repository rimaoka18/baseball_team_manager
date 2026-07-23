@php
	$ab = $player->gameStats->sum('at_bats');
	$hits = $player->gameStats->sum('hits');
	$avg = $ab > 0 ? round($hits / $ab, 3) : null;

	$ip = $player->gameStats->sum('innings_pitched');
	$er = $player->gameStats->sum('earned_runs');
	$era = ($ip > 0 && $er !== null) ? round(($er * 9) / $ip, 2) : null;
@endphp

<div class="bg-bf-cream shadow-md rounded-xl p-6 text-bf-navy">
	<div class="mb-4">
		@if (!is_null($player->jersey_number))
			<p class="text-sm text-gray-500 mb-1">#{{ $player->jersey_number }}</p>
		@endif
		<h2 class="text-xl font-semibold text-bf-navy">{{ $player->name }} の成績</h2>
	</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
		<div>
			<h3 class="text-lg font-bold text-gray-800 mb-2">打撃成績</h3>
			<p class="mb-1 text-gray-800"><span class="font-medium">打率：</span>{{ is_null($avg) ? '-' : number_format($avg, 3) }}</p>
			<p class="mb-1 text-gray-800"><span class="font-medium">打数：</span>{{ $ab }}</p>
			<p class="mb-1 text-gray-800"><span class="font-medium">安打：</span>{{ $hits }}</p>
			<p class="mb-1 text-gray-800"><span class="font-medium">ホームラン：</span>{{ $player->gameStats->sum('home_runs') }}</p>
		</div>

		<div>
			<h3 class="text-lg font-bold text-gray-800 mb-2">投手成績</h3>
			<p class="mb-1 text-gray-800"><span class="font-medium">防御率：</span>{{ is_null($era) ? '-' : number_format($era, 2) }}</p>
			<p class="mb-1 text-gray-800"><span class="font-medium">投球回：</span>{{ $ip > 0 ? number_format($ip, 1) : '-' }}</p>
			<p class="mb-1 text-gray-800"><span class="font-medium">奪三振：</span>{{ $player->gameStats->sum('pitching_strikeouts') }}</p>
		</div>
	</div>
</div>
