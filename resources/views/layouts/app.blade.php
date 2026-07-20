<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blitz Fang</title>
    <script src="https://cdn.tailwindcss.com"></script> {{-- Use Tailwind via CDN --}}
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-4 py-4">
            <h1 class="text-2xl font-extrabold mb-3">Blitz Fang スコアボード</h1>
            <nav class="flex flex-wrap gap-2">
                <a href="{{ route('games.index') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ request()->routeIs('games.index', 'games.create', 'games.store', 'games.show', 'games.edit', 'games.update') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    試合一覧
                </a>
                <a href="{{ route('games.upcoming.index') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ request()->routeIs('games.upcoming.*') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    次の試合
                </a>
                <a href="{{ route('games.stats') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ request()->routeIs('games.stats', 'players.search', 'players.autocomplete') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
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
