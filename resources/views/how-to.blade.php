@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">使い方ガイド</h1>

    <section>
        <h2 class="text-lg font-semibold mb-2">① 試合を登録する</h2>
        <p class="text-gray-700">「アプリを使ってみる」ボタンからスコア入力ページへ進みます。試合日、相手チーム名、スコアを入力し、選手の打撃・投手成績を記録してください。</p>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-2">② 成績を保存する</h2>
        <p class="text-gray-700">「保存する」ボタンをクリックすると、ボックススコアが保存され、一覧に表示されます。</p>
        <p class="mt-2 text-sm text-green-700 bg-green-100 px-3 py-2 rounded">
            ✅ 全ての成績を入力する必要はありません。空欄でもOKです。未入力の成績は「0」として自動的に計算されます。
        </p>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-2">③ 成績を見る</h2>
        <p class="text-gray-700">試合の詳細を開くと、選手の打率やERAなどの成績が表示されます。</p>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-2">④ よくある質問</h2>
        <ul class="list-disc list-inside text-gray-700">
            <li>Q: 名前は「山田 太郎」形式じゃないとダメですか？<br>→ A: はい、フルネームで入力してください。</li>
            <li>Q: 打席数を入力しないといけませんか？<br>→ A: 入力は任意ですが、打率を計算するには必要です。</li>
        </ul>
    </section>
</div>
@endsection
