<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blitz Fang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bf-navy text-gray-200">
    <header class="border-b border-white/10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex flex-col items-center">
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Blitz Fang" class="w-14 h-14 rounded-full object-cover border border-white/20">
                <h1 class="text-2xl font-extrabold text-bf-cream">Blitz Fang スコアボード</h1>
            </div>

            <div class="flex flex-col items-center gap-0.5 mb-3">
                <p class="text-sm text-gray-300">
                    {{ $teamWins }}勝 {{ $teamLosses }}敗 | 勝率 {{ $teamWinRate !== null ? ltrim(number_format($teamWinRate, 3), '0') : '-' }}
                </p>
                @if ($nextGame)
                    <p class="text-sm text-gray-300">
                        <span class="text-gray-400">次の試合</span>
                        {{ \Illuminate\Support\Carbon::parse($nextGame->game_date)->format('n月j日') }} vs {{ $nextGame->opponent ?? '未定' }}
                    </p>
                @endif
            </div>

            <nav class="flex gap-1 bg-white/10 rounded-xl p-1">
                <a href="{{ route('games.index') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('games.index', 'games.create', 'games.store', 'games.show', 'games.edit', 'games.update') ? 'bg-bf-cream text-bf-navy font-semibold shadow-sm' : 'text-gray-300 font-medium hover:text-white' }}">
                    試合
                </a>
                <a href="{{ route('games.upcoming.index') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('games.upcoming.*') ? 'bg-bf-cream text-bf-navy font-semibold shadow-sm' : 'text-gray-300 font-medium hover:text-white' }}">
                    スケジュール
                </a>
                <a href="{{ route('roster.index') }}"
                    class="px-4 py-1.5 rounded-lg text-sm transition {{ request()->routeIs('roster.*', 'players.search', 'players.autocomplete') ? 'bg-bf-cream text-bf-navy font-semibold shadow-sm' : 'text-gray-300 font-medium hover:text-white' }}">
                    選手
                </a>
            </nav>
        </div>
    </header>

    <div class="max-w-4xl mx-auto py-6 px-4">
        @yield('content')
    </div>

    <script>
        document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            }, 3500);
        });
    </script>
</body>
</html>
