<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Lineup;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpcomingGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_loads(): void
    {
        $response = $this->get(route('games.upcoming.create'));

        $response->assertStatus(200);
        $response->assertSee('スターティングラインナップ');
    }

    public function test_storing_an_upcoming_game_creates_players_and_lineup_in_batting_order(): void
    {
        $response = $this->post(route('games.upcoming.store'), [
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'player_names' => ['山田 太郎', '鈴木 一郎', ''],
            'position' => ['P', 'C', ''],
        ]);

        $response->assertRedirect(route('games.index'));

        $this->assertDatabaseHas('games', [
            'opponent' => 'Rival Sharks',
            'team_score' => null,
            'opponent_score' => null,
        ]);

        $game = Game::where('opponent', 'Rival Sharks')->firstOrFail();

        $this->assertSame(2, $game->lineups()->count());
        $this->assertDatabaseHas('lineups', [
            'game_id' => $game->id,
            'batting_order' => 1,
            'position' => 'P',
        ]);
        $this->assertDatabaseHas('lineups', [
            'game_id' => $game->id,
            'batting_order' => 2,
            'position' => 'C',
        ]);

        $this->assertSame('山田 太郎', Player::whereHas('lineups', fn ($q) => $q->where('batting_order', 1))->first()->name);
    }

    public function test_storing_upcoming_games_with_a_repeated_player_name_reuses_the_same_player(): void
    {
        $this->post(route('games.upcoming.store'), [
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'player_names' => ['山田 太郎'],
            'position' => ['P'],
        ]);

        $this->post(route('games.upcoming.store'), [
            'game_date' => '2026-08-08',
            'location' => 'Test Field',
            'opponent' => 'Other Team',
            'player_names' => ['山田 太郎'],
            'position' => ['P'],
        ]);

        $this->assertSame(1, Player::where('name', '山田 太郎')->count());
        $this->assertSame(2, Lineup::whereHas('player', fn ($q) => $q->where('name', '山田 太郎'))->count());
    }

    public function test_storing_an_upcoming_game_accepts_a_fullwidth_space_between_surname_and_given_name(): void
    {
        $response = $this->post(route('games.upcoming.store'), [
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'player_names' => ['今岡　稓'],
            'position' => ['P'],
        ]);

        $response->assertRedirect(route('games.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('players', ['name' => '今岡　稓']);
    }

    public function test_storing_an_upcoming_game_rejects_more_than_twenty_players(): void
    {
        $names = array_fill(0, 21, '山田 太郎');
        $positions = array_fill(0, 21, 'P');

        $response = $this->post(route('games.upcoming.store'), [
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'player_names' => $names,
            'position' => $positions,
        ]);

        $response->assertSessionHasErrors('player_names');
        $this->assertDatabaseMissing('games', ['opponent' => 'Rival Sharks']);
    }

    public function test_games_index_shows_the_first_nine_lineup_spots_and_hides_the_rest(): void
    {
        $game = Game::create([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Many Batters FC',
        ]);

        foreach (range(1, 12) as $order) {
            $player = Player::create(['name' => "Batter {$order}"]);
            Lineup::create([
                'game_id' => $game->id,
                'player_id' => $player->id,
                'batting_order' => $order,
                'position' => 'DH',
            ]);
        }

        $response = $this->get(route('games.index'));

        $response->assertStatus(200);
        $response->assertSee('Many Batters FC');
        $response->assertSee('onclick="toggleLineupPreview(this)"', false);

        // 3 batters beyond the starting nine should be marked hidden.
        $this->assertSame(3, substr_count($this->lineupListHtml($response->getContent()), 'upcoming-lineup-extra'));
    }

    public function test_games_index_does_not_show_more_link_when_lineup_has_nine_or_fewer(): void
    {
        $game = Game::create([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Exactly Nine FC',
        ]);

        foreach (range(1, 9) as $order) {
            $player = Player::create(['name' => "Batter {$order}"]);
            Lineup::create([
                'game_id' => $game->id,
                'player_id' => $player->id,
                'batting_order' => $order,
                'position' => 'DH',
            ]);
        }

        $response = $this->get(route('games.index'));

        $response->assertStatus(200);
        $response->assertDontSee('onclick="toggleLineupPreview(this)"', false);
        $this->assertSame(0, substr_count($this->lineupListHtml($response->getContent()), 'upcoming-lineup-extra'));
    }

    private function lineupListHtml(string $html): string
    {
        $start = strpos($html, '<ul id="upcoming-lineup-preview">');
        $end = strpos($html, '</ul>', $start);

        return substr($html, $start, $end - $start);
    }

    public function test_edit_page_shows_lineup_players_for_a_game_with_no_stats_yet(): void
    {
        $game = Game::create([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
        ]);

        $player = Player::create(['name' => '山田 太郎']);
        Lineup::create([
            'game_id' => $game->id,
            'player_id' => $player->id,
            'batting_order' => 1,
            'position' => 'P',
        ]);

        $response = $this->get(route('games.edit', $game));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('name="stat_ids[]" value=""', false);
    }

    public function test_updating_a_lineup_only_game_creates_stats_and_completes_the_game(): void
    {
        $game = Game::create([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
        ]);

        $player = Player::create(['name' => '山田 太郎']);
        Lineup::create([
            'game_id' => $game->id,
            'player_id' => $player->id,
            'batting_order' => 1,
            'position' => 'P',
        ]);

        $response = $this->put(route('games.update', $game), [
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'team_score' => 5,
            'opponent_score' => 2,
            'player_names' => ['山田 太郎'],
            'stat_ids' => [''],
            'ab' => [4],
            'h' => [2],
        ]);

        $response->assertRedirect(route('games.index'));

        $game->refresh();
        $this->assertSame(5, $game->team_score);

        $this->assertDatabaseHas('player_game_stats', [
            'game_id' => $game->id,
            'player_id' => $player->id,
            'at_bats' => 4,
            'hits' => 2,
        ]);

        // No duplicate player should have been created for the existing lineup name.
        $this->assertSame(1, Player::where('name', '山田 太郎')->count());
    }

    public function test_games_index_shows_message_when_no_upcoming_game(): void
    {
        Game::create([
            'game_date' => '2026-01-01',
            'location' => 'Past Field',
            'opponent' => 'Old Rivals',
            'team_score' => 5,
            'opponent_score' => 2,
        ]);

        $response = $this->get(route('games.index'));

        $response->assertStatus(200);
        $response->assertSee('予定されている試合はありません');
    }
}
