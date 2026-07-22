<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Lineup;
use App\Models\Player;
use App\Models\PlayerGameStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_does_not_show_use_previous_lineup_button_when_no_lineup_exists(): void
    {
        $response = $this->get(route('games.create'));

        $response->assertStatus(200);
        $response->assertDontSee('前回のスタメンを使う');
    }

    public function test_create_form_shows_use_previous_lineup_button_when_a_lineup_exists(): void
    {
        $game = Game::create(['game_date' => '2026-07-20', 'location' => 'Field A', 'opponent' => 'Rival Sharks']);
        $player = Player::create(['name' => '山田 太郎']);
        Lineup::create(['game_id' => $game->id, 'player_id' => $player->id, 'batting_order' => 1, 'position' => 'P']);

        $response = $this->get(route('games.create'));

        $response->assertStatus(200);
        $response->assertSee('前回のスタメンを使う');
        $response->assertSee('Rival Sharks');
    }

    private function validGamePayload(array $overrides = []): array
    {
        return array_merge([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'team_score' => 5,
            'opponent_score' => 2,
            'player_names' => ['山田 太郎'],
            'ab' => [4],
            'h' => [2],
        ], $overrides);
    }

    public function test_storing_games_with_a_repeated_player_name_reuses_the_same_player(): void
    {
        $this->post(route('games.store'), $this->validGamePayload());
        $this->post(route('games.store'), $this->validGamePayload(['opponent' => 'Other Team']));

        $this->assertSame(1, Player::where('name', '山田 太郎')->count());

        $player = Player::where('name', '山田 太郎')->firstOrFail();
        $this->assertSame(2, PlayerGameStat::where('player_id', $player->id)->count());
    }

    public function test_storing_a_game_accepts_a_fullwidth_space_between_surname_and_given_name(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['今岡　稓'],
        ]));

        $response->assertRedirect(route('games.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('players', ['name' => '今岡　稓']);
    }

    public function test_storing_a_game_accepts_a_single_name(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['山田'],
        ]));

        $response->assertRedirect(route('games.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('players', ['name' => '山田']);
    }

    public function test_storing_a_game_rejects_duplicate_player_names_in_the_same_submission(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['今岡', '今岡'],
            'ab' => [4, 3],
            'h' => [2, 1],
        ]));

        $response->assertSessionHasErrors('player_names.1');
        $this->assertSame(0, Player::where('name', '今岡')->count());
    }

    public function test_storing_a_game_rejects_duplicate_player_names_that_differ_only_by_whitespace(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['今岡', '今岡　'],
            'ab' => [4, 3],
            'h' => [2, 1],
        ]));

        $response->assertSessionHasErrors('player_names.1');
        $this->assertSame(0, Player::where('name', '今岡')->count());
    }

    public function test_storing_games_with_an_existing_single_name_reuses_the_same_player(): void
    {
        $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['今岡'],
        ]));
        $this->post(route('games.store'), $this->validGamePayload([
            'opponent' => 'Other Team',
            'player_names' => ['今岡'],
        ]));

        $this->assertSame(1, Player::where('name', '今岡')->count());
    }

    public function test_storing_a_game_saves_player_positions(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['山田', '鈴木'],
            'position' => ['SS', 'P'],
            'ab' => [4, 0],
            'h' => [2, 0],
            'ip' => [0, 5],
        ]));

        $response->assertRedirect(route('games.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('player_game_stats', [
            'position' => 'SS',
        ]);
        $this->assertDatabaseHas('player_game_stats', [
            'position' => 'P',
        ]);
    }

    public function test_show_page_displays_position_next_to_player_name(): void
    {
        $this->post(route('games.store'), $this->validGamePayload([
            'player_names' => ['山田'],
            'position' => ['CF'],
            'ab' => [4],
            'h' => [2],
        ]));

        $game = \App\Models\Game::firstOrFail();
        $response = $this->get(route('games.show', $game));

        $response->assertStatus(200);
        $response->assertSee('山田', false);
        $response->assertSee('CF', false);
        $response->assertDontSee('>守備位置<', false);
    }
}
