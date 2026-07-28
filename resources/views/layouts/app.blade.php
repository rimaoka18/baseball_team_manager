<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blitz Fang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=archivo:400,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bf-bg text-bf-ink overflow-x-hidden">
    <header class="border-b-2 border-bf-divider">
        <div class="max-w-4xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-6 gap-y-2">
            <a href="{{ route('games.index') }}" class="font-heading text-lg font-extrabold tracking-wide text-bf-navy mr-auto">
                BLITZ FANG
            </a>

            <nav class="flex items-center gap-6 text-sm">
                <a href="{{ route('games.index') }}"
                    class="bf-nav-link {{ request()->routeIs('games.index', 'games.create', 'games.store', 'games.show', 'games.edit', 'games.update') ? 'bf-nav-link-active' : '' }}">
                    試合
                </a>
                <a href="{{ route('games.upcoming.index') }}"
                    class="bf-nav-link {{ request()->routeIs('games.upcoming.*') ? 'bf-nav-link-active' : '' }}">
                    スケジュール
                </a>
                <a href="{{ route('roster.index') }}"
                    class="bf-nav-link {{ request()->routeIs('roster.*', 'players.search', 'players.autocomplete') ? 'bf-nav-link-active' : '' }}">
                    選手
                </a>
            </nav>

            <div class="text-xs">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-bf-navy underline">ログアウト</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-bf-navy underline">ログイン</a>
                @endauth
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 pb-3 text-xs text-gray-500">
            {{ $teamWins }}勝 {{ $teamLosses }}敗 ・ 勝率 {{ $teamWinRate !== null ? ltrim(number_format($teamWinRate, 3), '0') : '-' }}
            @if ($nextGame)
                <span class="text-gray-300 mx-1.5">|</span>
                次の試合 {{ \Illuminate\Support\Carbon::parse($nextGame->game_date)->format('n月j日') }} vs {{ $nextGame->opponent ?? '未定' }}
            @endif
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
