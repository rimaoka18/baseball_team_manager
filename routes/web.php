<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\PlayerController;

Route::view('/', 'landing');
Route::view('/how-to', 'how-to')->name('how-to');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::group(['prefix' => 'games', 'as' => 'games.'], function () {
    Route::get('/', [GamesController::class, 'index'])->name('index');         // ゲーム一覧
    Route::get('create', [GamesController::class, 'create'])->name('create')->middleware('auth');  // 新しい試合（＋成績）を追加
    Route::post('/', [GamesController::class, 'store'])->name('store')->middleware('auth');        // 試合の保存

    Route::get('upcoming', [GamesController::class, 'upcoming'])->name('upcoming.index');               // 次の試合タブ
    Route::get('upcoming/create', [GamesController::class, 'createUpcoming'])->name('upcoming.create')->middleware('auth'); // 次の試合の予定を追加
    Route::post('upcoming', [GamesController::class, 'storeUpcoming'])->name('upcoming.store')->middleware('auth');         // 次の試合の予定を保存
    Route::get('upcoming/{game}/edit', [GamesController::class, 'editUpcoming'])->name('upcoming.edit')->middleware('auth');   // 次の試合の予定を編集
    Route::put('upcoming/{game}', [GamesController::class, 'updateUpcoming'])->name('upcoming.update')->middleware('auth');    // 次の試合の予定の更新

    Route::get('stats', fn () => redirect()->route('roster.index'))->name('stats'); // 旧URL互換

    Route::get('{game}', [GamesController::class, 'show'])->name('show');      // ボックススコア表示（詳細）

    Route::get('{game}/edit', [GamesController::class, 'edit'])->name('edit')->middleware('auth'); // 編集画面（必要なら）
    Route::put('{game}', [GamesController::class, 'update'])->name('update')->middleware('auth');  // 編集の保存

    Route::delete('{game}', [GamesController::class, 'destroy'])->name('destroy')->middleware('auth');
});

Route::get('/roster', [PlayerController::class, 'roster'])->name('roster.index');
Route::post('/roster/players', [PlayerController::class, 'store'])->name('roster.players.store')->middleware('auth');
Route::get('/roster/players/{player}', [PlayerController::class, 'show'])->name('roster.players.show');
Route::get('/roster/players/{player}/edit', [PlayerController::class, 'edit'])->name('roster.players.edit')->middleware('auth');
Route::put('/roster/players/{player}', [PlayerController::class, 'update'])->name('roster.players.update')->middleware('auth');

// プレイヤー検索
Route::get('/players/search', [PlayerController::class, 'search'])->name('players.search');
Route::get('/players/autocomplete', [PlayerController::class, 'autocomplete'])->name('players.autocomplete');
