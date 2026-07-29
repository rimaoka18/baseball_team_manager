@extends('layouts.app')

@section('content')

<div class="max-w-[980px] mx-auto">

<a href="{{ route('roster.index') }}" class="inline-flex items-center gap-1.5 text-sm mb-4 hover:underline">
	<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
	選手一覧に戻る
</a>

<h1 class="font-heading text-3xl font-extrabold mb-6">成績ランキング</h1>

<div class="grid gap-6" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
	<div>
		<h5 class="font-heading font-bold pb-2 mb-2 border-b-2 border-bf-divider">打率ランキング</h5>
		@forelse ($topBatters as $entry)
			<div class="flex justify-between items-center py-3 border-b border-bf-divider">
				<span>{{ $loop->iteration }}位　{{ $entry['player']->name }}</span>
				<span class="font-heading font-extrabold text-bf-navy">{{ number_format($entry['avg'], 3) }}</span>
			</div>
		@empty
			<p class="text-sm text-bf-ink/60 text-center py-6">まだ記録がありません</p>
		@endforelse
	</div>
	<div>
		<h5 class="font-heading font-bold pb-2 mb-2 border-b-2 border-bf-divider">防御率ランキング</h5>
		@forelse ($topPitchers as $entry)
			<div class="flex justify-between items-center py-3 border-b border-bf-divider">
				<span>{{ $loop->iteration }}位　{{ $entry['player']->name }}</span>
				<span class="font-heading font-extrabold text-bf-navy">{{ number_format($entry['era'], 2) }}</span>
			</div>
		@empty
			<p class="text-sm text-bf-ink/60 text-center py-6">まだ記録がありません</p>
		@endforelse
	</div>
</div>

</div>

@endsection
