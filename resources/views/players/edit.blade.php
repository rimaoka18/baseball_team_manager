@extends('layouts.app')

@section('content')

<div class="max-w-[560px] mx-auto">

<a href="{{ route('roster.players.show', $player) }}" class="inline-flex items-center gap-1.5 text-sm mb-4 hover:underline">
	<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
	選手詳細に戻る
</a>

<h1 class="font-heading text-3xl font-extrabold mb-6">選手を編集</h1>

@include('partials.validation-errors')

<form method="POST" action="{{ route('roster.players.update', $player) }}" enctype="multipart/form-data">
	@csrf
	@method('PUT')

	<div class="flex gap-4 flex-wrap items-center mb-5">
		<label id="photo-dropzone" for="photo-input"
			class="w-24 h-24 rounded-full border border-dashed border-bf-divider bg-bf-cream shrink-0 flex flex-col items-center justify-center text-center cursor-pointer overflow-hidden">
			<img id="photo-preview" class="{{ $player->photoUrl() ? '' : 'hidden' }} w-full h-full object-cover" alt="" src="{{ $player->photoUrl() }}">
			<span id="photo-placeholder" class="{{ $player->photoUrl() ? 'hidden' : '' }} flex flex-col items-center gap-1 px-2 text-bf-ink/45">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
				<span class="text-xs leading-tight">ドラッグ<br>or <span class="underline">browse files</span></span>
			</span>
		</label>
		<input type="file" id="photo-input" name="photo" accept="image/*" class="hidden">
		<p class="text-xs text-bf-ink/60 flex-1 min-w-[180px]">既存の写真をドラッグ&ドロップ、またはクリックで直接差し替えできます。顔写真・プレー中の写真どちらでも登録可能です（正方形推奨、自動でトリミングされます）。</p>
	</div>

	<div class="flex gap-3 items-end flex-wrap mb-6">
		<div class="w-[100px] shrink-0">
			<label class="block text-xs text-bf-ink/60 mb-1">背番号</label>
			<input type="text" inputmode="numeric" pattern="[0-9]{1,2}" maxlength="2" name="jersey_number" value="{{ old('jersey_number', $player->jersey_number) }}" placeholder="18" class="bf-input">
		</div>
		<div class="flex-1 min-w-[180px]">
			<label class="block text-xs text-bf-ink/60 mb-1">選手名</label>
			<input type="text" name="name" value="{{ old('name', $player->name) }}" required class="bf-input">
		</div>
	</div>

	<div class="flex items-center justify-between gap-3 flex-wrap">
		<button type="submit" class="bf-btn bf-btn-primary px-8">保存する</button>
		<button type="submit" form="player-delete-form" class="bf-btn bf-btn-ghost text-bf-danger">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
			選手を削除
		</button>
	</div>
</form>

<form id="player-delete-form" action="{{ route('roster.players.destroy', $player) }}" method="POST"
	onsubmit="return confirm('本当に削除しますか？成績データも含めて元に戻せません。');">
	@csrf
	@method('DELETE')
</form>

</div>

<script>
	(function () {
		const dropzone = document.getElementById('photo-dropzone');
		const input = document.getElementById('photo-input');
		const preview = document.getElementById('photo-preview');
		const placeholder = document.getElementById('photo-placeholder');

		function showFile(file) {
			if (!file || !file.type.startsWith('image/')) return;

			const reader = new FileReader();
			reader.onload = () => {
				preview.src = reader.result;
				preview.classList.remove('hidden');
				placeholder.classList.add('hidden');
			};
			reader.readAsDataURL(file);
		}

		input.addEventListener('change', () => showFile(input.files[0]));

		dropzone.addEventListener('dragover', (e) => {
			e.preventDefault();
			dropzone.classList.add('border-bf-navy');
		});

		dropzone.addEventListener('dragleave', () => {
			dropzone.classList.remove('border-bf-navy');
		});

		dropzone.addEventListener('drop', (e) => {
			e.preventDefault();
			dropzone.classList.remove('border-bf-navy');

			const file = e.dataTransfer.files[0];
			if (!file) return;

			input.files = e.dataTransfer.files;
			showFile(file);
		});
	})();
</script>

@endsection
