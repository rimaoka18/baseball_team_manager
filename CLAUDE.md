# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Blitz Fang is a Laravel 12 app for tracking baseball games and player stats: recording box scores per game, and surfacing batting-average / ERA leaderboards. UI copy/validation messages are in Japanese.

## Commands

```bash
# Install
composer install
npm install

# Run everything (server + queue listener + logs + vite), matches `composer run dev`
composer run dev

# Or individually
php artisan serve
npm run dev          # vite dev server
npm run build         # vite production build

# Database
php artisan migrate
php artisan migrate:fresh

# Tests (PHPUnit, not Pest)
php artisan test
php artisan test --filter=TestName
vendor/bin/phpunit tests/Feature/ExampleTest.php

# Tinker / artisan
php artisan tinker
```

DB connection is SQLite by default (`DB_CONNECTION=sqlite`, see `.env.example`).

## Architecture

**Domain model**: `Game` hasMany `PlayerGameStat` (one row per player per game, holding both batting and pitching stats), `PlayerGameStat` belongsTo `Player`. A single `PlayerGameStat` row covers both hitting columns (`at_bats`, `runs`, `hits`, `rbi`, `home_runs`, `walks`, `strikeouts`) and pitching columns (`innings_pitched`, `hits_allowed`, `earned_runs`, `pitching_walks`, `pitching_strikeouts`) — a row is treated as a pitching line when `innings_pitched` is not null (see `GamesController::show`).

**Upcoming (scheduled) games**: a `Game` with `team_score`/`opponent_score` still `null` is treated as an "upcoming game" — there's no separate status column, the null score *is* the scheduled/completed distinction. `GamesController::index` queries the soonest such game (`whereNull('team_score')->orderBy('game_date')`) and passes it to `games/index.blade.php` as `$upcomingGame`, which renders its `Lineup` rows (batting order + position). Once a real box score is entered for that game via the normal edit/update flow, `team_score` gets set and it stops being "upcoming." `Lineup` is only populated through `GamesController::createUpcoming`/`storeUpcoming` (routes `games.upcoming.create`/`games.upcoming.store`) — completed-game box scores (`GamesController::store`) never touch `Lineup`, only `PlayerGameStat`.

**Player creation is deduplicated by exact name match**: both `GamesController::store` (box score entry) and `GamesController::storeUpcoming` (lineup entry) use `Player::firstOrCreate(['name' => $name])`, so typing the same name again reuses the existing `Player` row instead of creating a duplicate — this matters because `PlayerStatService` aggregates batting average/ERA per `Player` id, and duplicates would silently split a real player's stats across rows. The matching is a literal string match on `name`, so inconsistent spacing/spelling still creates a new row — there's no fuzzy matching.

**Stat aggregation** (`app/Services/PlayerStatService.php`) computes batting average and ERA by summing `PlayerGameStat` across all games per player in PHP (via `Player::with('gameStats')->get()->map(...)`), not in SQL.

**Routes** (`routes/web.php`): resourceful routes under `games.*` (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`), plus `games.upcoming.create`/`games.upcoming.store` for scheduling a game + lineup, and a standalone `players.search` route. No auth/middleware is applied — everything is open.

**Form requests**: `StoreGameRequest`/`UpdateGameRequest` validate the completed-game + per-row stat arrays (`ab.*`, `h.*`, `ip.*`, etc., indexed in parallel with `player_names`). `StoreUpcomingGameRequest` validates the scheduled-game + lineup arrays (`player_names`, `position`), capped at `max:20` entries. `player_names.*` in `StoreGameRequest`/`StoreUpcomingGameRequest` requires two space-separated words (Japanese 姓 + 名) via regex — `UpdateGameRequest` does not enforce this.

**Views**: server-rendered Blade only (`resources/views/games/*`, `players/search.blade.php`, `landing.blade.php`, `how-to.blade.php`) — no SPA/API layer, no JS framework (Alpine/Vue/etc.) — dynamic bits (add-row buttons, the upcoming-lineup "show more" toggle) are plain inline `<script>` blocks following the existing `addPlayerRow()`-style pattern. Frontend build is Vite + Tailwind v4 (`vite.config.js`).

**Tests**: `tests/Feature/GameStoreTest.php` and `tests/Feature/UpcomingGameTest.php` cover the player-dedup behavior, upcoming-game lineup storage/validation, and the games-index upcoming-game card; `tests/Feature/ExampleTest.php`/`tests/Unit/ExampleTest.php` are the untouched Laravel defaults. Feature tests run against an isolated in-memory SQLite DB via `.env.testing` (`APP_ENV=testing`) — they do **not** touch the real dev database (`DB_CONNECTION=mysql` in `.env`, used by the `docker-compose.yml` stack).
