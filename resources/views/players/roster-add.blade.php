@extends('layouts.app')

@section('content')

<div class="max-w-[560px] mx-auto">

<a href="{{ route('roster.index') }}" class="inline-flex items-center gap-1.5 text-sm mb-4 hover:underline">
	<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
	選手一覧に戻る
</a>

<h1 class="font-heading text-3xl font-extrabold mb-6">選手を追加</h1>

@include('partials.validation-errors')

<form method="POST" action="{{ route('roster.players.store') }}">
	@csrf
	<div class="flex gap-3 items-end flex-wrap mb-3">
		<div class="w-[100px] shrink-0">
			<label class="block text-xs text-bf-ink/60 mb-1">背番号</label>
			<input type="text" inputmode="numeric" pattern="[0-9]{1,2}" maxlength="2" name="jersey_number" value="{{ old('jersey_number') }}" placeholder="18" class="bf-input">
		</div>
		<div class="flex-1 min-w-[180px]">
			<label class="block text-xs text-bf-ink/60 mb-1">選手名</label>
			<input type="text" name="name" value="{{ old('name') }}" required placeholder="例：山田" class="bf-input">
		</div>
	</div>
	<button type="submit" class="bf-btn bf-btn-primary w-full justify-center h-10">追加する</button>
	<p class="text-sm text-bf-ink/60 mt-3">ここに登録した選手は、スケジュールのスタメン選択で選べます。</p>
</form>

</div>

@endsection
