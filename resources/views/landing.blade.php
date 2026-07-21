<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyDugout - 野球スコア管理アプリ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .landing-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .landing-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.35;
        }

        .landing-bg-overlay {
            position: absolute;
            inset: 0;
            background: rgba(27, 35, 64, 0.55);
        }

        .landing-content {
            position: relative;
            z-index: 10;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-bf-navy text-white">

    <div class="landing-bg" aria-hidden="true">
        <img
            src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExb2ZsNGV6d2doZmNtenF5eDhrZjBqYjZkM256MTh5MnEzaHU0NHhqaiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/jQst100Uo5iPEjwd1M/giphy.gif"
            alt=""
        >
        <div class="landing-bg-overlay"></div>
    </div>

    <div class="landing-content">
        <div class="w-full max-w-4xl mx-auto" style="display: flex; flex-direction: column; gap: 3.5rem;">
            <section class="text-center" style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <h1 class="text-4xl md:text-5xl font-bold drop-shadow-[0_1px_2px_rgba(255,255,255,0.7)]">
                    MyDugout
                </h1>
                <p class="text-lg text-gray-200 max-w-2xl mx-auto">
                    チームの試合結果・選手成績を簡単に記録＆共有できる野球スコア管理アプリ
                </p>
                <a href="{{ route('games.index') }}"
                   class="inline-block bg-bf-cream text-bf-navy px-6 py-3 rounded-full shadow font-semibold hover:bg-bf-gold/20 transition"
                   style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                    アプリを使ってみる
                </a>
            </section>

            <section class="grid md:grid-cols-3 gap-6 text-center text-gray-100">
                <div class="p-4 border border-white/20 rounded-xl shadow-sm bg-white/10 backdrop-blur">
                    <h3 class="font-semibold text-lg mb-2">ボックススコア入力</h3>
                    <p class="text-sm text-gray-300">試合後に選手の打撃・投手成績を素早く記録。</p>
                </div>
                <div class="p-4 border border-white/20 rounded-xl shadow-sm bg-white/10 backdrop-blur">
                    <h3 class="font-semibold text-lg mb-2">モバイル対応</h3>
                    <p class="text-sm text-gray-300">スマホでもスムーズに操作可能。どこでもスコア管理。</p>
                </div>
                <div class="p-4 border border-white/20 rounded-xl shadow-sm bg-white/10 backdrop-blur">
                    <h3 class="font-semibold text-lg mb-2">成績ランキング</h3>
                    <p class="text-sm text-gray-300">打率・ERAの上位選手を自動集計。</p>
                </div>
            </section>

            <footer class="text-center text-xs text-gray-300">
                &copy; 2025 MyDugout. Built by Ryo Imaoka.
                <br>
                <a href="{{ route('how-to') }}" class="text-bf-gold hover:underline">使い方ガイド</a>
            </footer>
        </div>
    </div>

</body>
</html>
