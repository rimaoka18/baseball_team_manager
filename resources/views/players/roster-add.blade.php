@extends('layouts.app')

@section('content')

<div class="max-w-[560px] mx-auto">

<a href="{{ route('roster.index') }}" class="inline-flex items-center gap-1.5 text-sm mb-4 hover:underline">
	<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
	選手一覧に戻る
</a>

<h1 class="font-heading text-3xl font-extrabold mb-6">選手を追加</h1>

@include('partials.validation-errors')

<form method="POST" action="{{ route('roster.players.store') }}" enctype="multipart/form-data">
	@csrf

	<div class="flex gap-4 flex-wrap items-start mb-5">
		<label id="photo-dropzone" for="photo-input"
			class="w-32 h-32 rounded-full border border-dashed border-bf-divider bg-bf-cream shrink-0 flex flex-col items-center justify-center text-center cursor-pointer overflow-hidden">
			<img id="photo-preview" class="hidden w-full h-full object-cover" alt="">
			<span id="photo-placeholder" class="flex flex-col items-center gap-1 px-2 text-bf-ink/45">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
				<span class="text-xs leading-tight">顔写真をドラッグ<br>or <span class="underline">browse files</span></span>
			</span>
		</label>
		<input type="file" id="photo-input" name="photo" accept="image/*" class="hidden">
		<p class="text-xs text-bf-ink/60 flex-1 min-w-[180px]">正方形の写真がおすすめです（自動で円形にトリミングされます）。帽子・サングラス着用のままで構いません。</p>
	</div>

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
