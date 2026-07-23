@extends('layouts.app')

@section('content')

<div class="space-y-6">

<div class="flex items-center justify-between">
	<h2 class="text-lg font-bold text-bf-cream">選手を編集</h2>
	<a href="{{ route('roster.players.show', $player) }}"
		class="inline-block bg-bf-cream hover:bg-bf-gold/20 text-bf-navy text-sm font-semibold px-4 py-1.5 rounded-full transition">
		詳細に戻る
	</a>
</div>

@include('partials.validation-errors')

<div class="bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	<form method="POST" action="{{ route('roster.players.update', $player) }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
		@csrf
		@method('PUT')
		<div class="w-24 shrink-0">
			<label class="block text-sm font-medium text-bf-navy mb-1">背番号</label>
			<input type="number" name="jersey_number" value="{{ old('jersey_number', $player->jersey_number) }}" min="0" max="99"
				placeholder="18"
				class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
		</div>
		<div class="flex-1">
			<label class="block text-sm font-medium text-bf-navy mb-1">選手名</label>
			<input type="text" name="name" value="{{ old('name', $player->name) }}" required
				class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
		</div>
		<button type="submit"
			class="bg-bf-navy text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-bf-navy-light transition">
			保存する
		</button>
	</form>
</div>

</div>

@endsection
