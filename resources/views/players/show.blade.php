@extends('layouts.app')

@section('content')

<div class="max-w-[980px] mx-auto">

@if (session('success'))
	<div data-auto-dismiss class="bg-bf-cream text-bf-navy border border-bf-divider p-3 mb-6 font-medium">{{ session('success') }}</div>
@endif

@include('partials.validation-errors')

<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
	<h1 class="font-heading text-3xl font-extrabold">選手詳細</h1>
	<a href="{{ route('roster.index') }}" class="bf-btn bf-btn-secondary">選手一覧に戻る</a>
</div>

<div class="flex items-center gap-4 flex-wrap mb-2">
	<span class="w-[84px] h-[84px] rounded-full border border-bf-divider bg-bf-cream shrink-0 overflow-hidden flex items-center justify-center text-bf-ink/45">
		@if ($player->photoUrl())
			<img src="{{ $player->photoUrl() }}" alt="" class="w-full h-full object-cover">
		@else
			<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-3.5 3.5-6 8-6s8 2.5 8 6"/></svg>
		@endif
	</span>
	<div>
		@if (!is_null($player->jersey_number))
			<div class="text-xs font-heading font-extrabold text-bf-ink/60">#{{ $player->jersey_number }}</div>
		@endif
		<h2 class="font-heading text-xl font-extrabold mt-0.5">{{ $player->name }} の成績</h2>
	</div>
</div>

<hr class="border-t-2 border-bf-divider my-6">

@php
	$ab = $player->gameStats->sum('at_bats');
	$hits = $player->gameStats->sum('hits');
	$avg = $ab > 0 ? round($hits / $ab, 3) : null;

	$ip = $player->gameStats->sum('innings_pitched');
	$er = $player->gameStats->sum('earned_runs');
	$era = ($ip > 0 && $er !== null) ? round(($er * 9) / $ip, 2) : null;
@endphp

<div class="grid gap-6 mb-6" style="grid-template-columns: 1fr 1fr;">
	<div>
		<h5 class="font-heading font-bold pb-2 mb-0 border-b-2 border-bf-divider">打撃成績</h5>
		<div class="flex justify-between py-2 border-b border-bf-divider text-sm">
			<span>打率</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ is_null($avg) ? '-' : number_format($avg, 3) }}</span>
		</div>
		<div class="flex justify-between py-2 border-b border-bf-divider text-sm">
			<span>打数</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ $ab }}</span>
		</div>
		<div class="flex justify-between py-2 border-b border-bf-divider text-sm">
			<span>安打</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ $hits }}</span>
		</div>
		<div class="flex justify-between py-2 text-sm">
			<span>ホームラン</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ $player->gameStats->sum('home_runs') }}</span>
		</div>
	</div>

	<div>
		<h5 class="font-heading font-bold pb-2 mb-0 border-b-2 border-bf-divider">投手成績</h5>
		<div class="flex justify-between py-2 border-b border-bf-divider text-sm">
			<span>防御率</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ is_null($era) ? '-' : number_format($era, 2) }}</span>
		</div>
		<div class="flex justify-between py-2 border-b border-bf-divider text-sm">
			<span>投球回</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ $ip > 0 ? number_format($ip, 1) : '-' }}</span>
		</div>
		<div class="flex justify-between py-2 text-sm">
			<span>奪三振</span>
			<span class="font-heading font-extrabold text-bf-navy">{{ $player->gameStats->sum('pitching_strikeouts') }}</span>
		</div>
	</div>
</div>

@auth
<a href="{{ route('roster.players.edit', $player) }}" class="bf-btn bf-btn-primary">編集する</a>
@endauth

</div>

@endsection
