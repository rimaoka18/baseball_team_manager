<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\PlayerController;

Route::view('/', 'landing');


Route::group(['prefix' => 'games', 'as' => 'games.'], function () {
    Route::get('/', [GamesController::class, 'index'])->name('index');         // ゲーム一覧
    Route::get('create', [GamesController::class, 'create'])->name('create');  // 新しい試合（＋成績）を追加
    Route::post('/', [GamesController::class, 'store'])->name('store');        // 試合の保存

    Route::get('{game}', [GamesController::class, 'show'])->name('show');      // ボックススコア表示（詳細）

    Route::get('{game}/edit', [GamesController::class, 'edit'])->name('edit'); // 編集画面（必要なら）
    Route::put('{game}', [GamesController::class, 'update'])->name('update');  // 編集の保存

    Route::delete('{game}', [GamesController::class, 'destroy'])->name('destroy');
});

// プレイヤー検索
Route::get('/players/search', [PlayerController::class, 'search'])->name('players.search');
