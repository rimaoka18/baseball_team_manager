@extends('layouts.app')

@section('content')

<div class="space-y-6">

<h2 class="text-lg font-bold">成績</h2>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

<div class="flex justify-center mb-6">
	<a href="{{ route('players.search') }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-2 rounded-full shadow-sm transition">
		プレイヤー検索
	</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
	<!-- Batting AVG -->
	<div class="bg-white shadow-md rounded-xl p-4">
		<h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">打率ランキング</h2>
		<ul>
			@php
				$medals = ['1位', '2位', '3位'];
				$loopIndex = 0;
			@endphp

			@foreach ($topBatters as $entry)
				<li class="flex justify-between items-center py-2 px-2 border-b last:border-none {{ $loopIndex === 0 ? 'border-l-4 border-bf-gold bg-bf-gold/10' : '' }}">
					<span class="font-medium">
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
				<li class="flex justify-between items-center py-2 px-2 border-b last:border-none {{ $loopIndex === 0 ? 'border-l-4 border-bf-gold bg-bf-gold/10' : '' }}">
					<span class="font-medium">
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
				<li class="text-gray-500">データがありません</li>
			@endif
		</ul>
	</div>
</div>

</div>

</div>

@endsection
