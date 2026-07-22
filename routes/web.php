<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\PlayerController;

Route::view('/', 'landing');
Route::view('/how-to', 'how-to')->name('how-to');


Route::group(['prefix' => 'games', 'as' => 'games.'], function () {
    Route::get('/', [GamesController::class, 'index'])->name('index');         // ゲーム一覧
    Route::get('create', [GamesController::class, 'create'])->name('create');  // 新しい試合（＋成績）を追加
    Route::post('/', [GamesController::class, 'store'])->name('store');        // 試合の保存

    Route::get('upcoming', [GamesController::class, 'upcoming'])->name('upcoming.index');               // 次の試合タブ
    Route::get('upcoming/create', [GamesController::class, 'createUpcoming'])->name('upcoming.create'); // 次の試合の予定を追加
    Route::post('upcoming', [GamesController::class, 'storeUpcoming'])->name('upcoming.store');         // 次の試合の予定を保存
    Route::get('upcoming/{game}/edit', [GamesController::class, 'editUpcoming'])->name('upcoming.edit');   // 次の試合の予定を編集
    Route::put('upcoming/{game}', [GamesController::class, 'updateUpcoming'])->name('upcoming.update');    // 次の試合の予定の更新

    Route::get('stats', fn () => redirect()->route('roster.index'))->name('stats'); // 旧URL互換

    Route::get('{game}', [GamesController::class, 'show'])->name('show');      // ボックススコア表示（詳細）

    Route::get('{game}/edit', [GamesController::class, 'edit'])->name('edit'); // 編集画面（必要なら）
    Route::put('{game}', [GamesController::class, 'update'])->name('update');  // 編集の保存

    Route::delete('{game}', [GamesController::class, 'destroy'])->name('destroy');
});

Route::get('/roster', [PlayerController::class, 'roster'])->name('roster.index');
Route::post('/roster/players', [PlayerController::class, 'store'])->name('roster.players.store');

// プレイヤー検索
Route::get('/players/search', [PlayerController::class, 'search'])->name('players.search');
Route::get('/players/autocomplete', [PlayerController::class, 'autocomplete'])->name('players.autocomplete');
