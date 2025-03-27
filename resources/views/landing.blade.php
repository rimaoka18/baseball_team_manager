<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyDugout - 野球スコア管理アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative bg-gray-900 text-white overflow-hidden">

    {{-- 🔥 Background GIF --}}
    <div class="absolute inset-0 z-0">
        <img
            src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExb2ZsNGV6d2doZmNtenF5eDhrZjBqYjZkM256MTh5MnEzaHU0NHhqaiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/jQst100Uo5iPEjwd1M/giphy.gif"
            alt="Baseball animation"
            class="w-full h-full object-cover opacity-30"
        >
    </div>

    {{-- 🌙 Overlay Content --}}
    <div class="relative z-10 max-w-4xl mx-auto px-4 py-12 space-y-16 bg-black/30 backdrop-blur-sm rounded-lg">

        {{-- Hero Section --}}
        <section class="text-center space-y-4">
            <h1 class="text-4xl font-bold drop-shadow-[0_1px_2px_rgba(255,255,255,0.7)]">
                MyDugout
            </h1>
            <p class="text-lg text-gray-300">
                チームの試合結果・選手成績を簡単に記録＆共有できる野球スコア管理アプリ
            </p>
            <a href="{{ route('games.index') }}"
               class="inline-block mt-4 bg-green-600 text-white px-6 py-3 rounded shadow hover:bg-green-700 transition">
                アプリを使ってみる
            </a>
        </section>

        {{-- Features --}}
        <section class="grid md:grid-cols-3 gap-6 text-center text-gray-100">
            <div class="p-4 border border-white/20 rounded shadow-sm bg-white/10 backdrop-blur">
                <h3 class="font-semibold text-lg mb-2">📊 ボックススコア入力</h3>
                <p class="text-sm text-gray-300">試合後に選手の打撃・投手成績を素早く記録。</p>
            </div>
            <div class="p-4 border border-white/20 rounded shadow-sm bg-white/10 backdrop-blur">
                <h3 class="font-semibold text-lg mb-2">📱 モバイル対応</h3>
                <p class="text-sm text-gray-300">スマホでもスムーズに操作可能。どこでもスコア管理。</p>
            </div>
            <div class="p-4 border border-white/20 rounded shadow-sm bg-white/10 backdrop-blur">
                <h3 class="font-semibold text-lg mb-2">🏅 成績ランキング</h3>
                <p class="text-sm text-gray-300">打率・ERAの上位選手を自動集計。</p>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="text-center text-xs text-gray-400">
            &copy; 2025 MyDugout. Built by Ryo Imaoka.
        </footer>
    </div>

</body>
</html>
