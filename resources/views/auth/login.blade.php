@extends('layouts.app')

@section('content')

<div class="max-w-sm mx-auto space-y-6">

<h2 class="text-lg font-bold text-bf-cream text-center">ログイン</h2>

@include('partials.validation-errors')

<div class="bg-bf-cream rounded-xl shadow-sm border border-gray-200 p-6">
	<form method="POST" action="{{ route('login.attempt') }}" class="flex flex-col gap-4">
		@csrf
		<div>
			<label class="block text-sm font-medium text-bf-navy mb-1">メールアドレス</label>
			<input type="email" name="email" value="{{ old('email') }}" required autofocus
				class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
		</div>
		<div>
			<label class="block text-sm font-medium text-bf-navy mb-1">パスワード</label>
			<input type="password" name="password" required
				class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-gray-800">
		</div>
		<button type="submit"
			class="bg-bf-navy text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-bf-navy-light transition">
			ログイン
		</button>
	</form>
	<p class="text-sm text-gray-500 mt-4 text-center">
		アカウントをお持ちでない場合は <a href="{{ route('register') }}" class="text-bf-navy underline">新規登録</a>
	</p>
</div>

</div>

@endsection
