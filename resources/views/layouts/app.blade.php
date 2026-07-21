<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blitz Fang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-col items-center">
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Blitz Fang" class="w-14 h-14 rounded-full object-cover border border-gray-200">
                <h1 class="text-2xl font-extrabold">Blitz Fang スコアボード</h1>
            </div>
            <nav class="flex gap-1 bg-gray-100 rounded-xl p-1">
                <a href="{{ route('games.index') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('games.index', 'games.create', 'games.store', 'games.show', 'games.edit', 'games.update') ? 'bg-white text-bf-navy font-semibold shadow-sm' : 'text-gray-500 font-medium hover:text-bf-navy' }}">
                    試合一覧
                </a>
                <a href="{{ route('games.upcoming.index') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('games.upcoming.*') ? 'bg-white text-bf-navy font-semibold shadow-sm' : 'text-gray-500 font-medium hover:text-bf-navy' }}">
                    次の試合
                </a>
                <a href="{{ route('games.stats') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('games.stats', 'players.search', 'players.autocomplete') ? 'bg-white text-bf-navy font-semibold shadow-sm' : 'text-gray-500 font-medium hover:text-bf-navy' }}">
                    成績
                </a>
            </nav>
        </div>
    </header>

    <div class="max-w-5xl mx-auto py-6 px-4">
        @yield('content')
    </div>
</body>
</html>
