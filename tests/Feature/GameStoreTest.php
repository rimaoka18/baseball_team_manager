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
    }

    private function validGamePayload(array $overrides = []): array
    {
        $player = Player::firstOrCreate(['name' => '山田 太郎']);

        return array_merge([
            'game_date' => '2026-08-01',
            'location' => 'Test Field',
            'opponent' => 'Rival Sharks',
            'team_score' => 5,
            'opponent_score' => 2,
            'player_ids' => [$player->id],
            'ab' => [4],
            'h' => [2],
        ], $overrides);
    }

    public function test_storing_games_reuses_the_same_roster_player(): void
    {
        $player = Player::create(['name' => '鈴木 一郎']);

        $this->post(route('games.store'), $this->validGamePayload(['player_ids' => [$player->id]]));
        $this->post(route('games.store'), $this->validGamePayload([
            'opponent' => 'Other Team',
            'player_ids' => [$player->id],
        ]));

        $this->assertSame(1, Player::where('name', '鈴木 一郎')->count());
        $this->assertSame(2, PlayerGameStat::where('player_id', $player->id)->count());
    }

    public function test_storing_a_game_rejects_duplicate_player_ids_in_the_same_submission(): void
    {
        $player = Player::create(['name' => '今岡']);

        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_ids' => [$player->id, $player->id],
            'ab' => [4, 3],
            'h' => [2, 1],
        ]));

        $response->assertSessionHasErrors('player_ids.1');
        $this->assertSame(0, PlayerGameStat::where('player_id', $player->id)->count());
    }

    public function test_storing_a_game_rejects_unknown_player_ids(): void
    {
        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_ids' => [999],
        ]));

        $response->assertSessionHasErrors('player_ids.0');
        $this->assertDatabaseMissing('games', ['opponent' => 'Rival Sharks']);
    }

    public function test_storing_a_game_saves_batting_order_matching_submission_order(): void
    {
        $yamada = Player::create(['name' => '山田']);
        $suzuki = Player::create(['name' => '鈴木']);
        $sato = Player::create(['name' => '佐藤']);

        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_ids' => [$yamada->id, $suzuki->id, $sato->id],
            'ab' => [4, 3, 2],
            'h' => [2, 1, 0],
        ]));

        $response->assertRedirect(route('games.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSame(1, PlayerGameStat::where('player_id', $yamada->id)->value('batting_order'));
        $this->assertSame(2, PlayerGameStat::where('player_id', $suzuki->id)->value('batting_order'));
        $this->assertSame(3, PlayerGameStat::where('player_id', $sato->id)->value('batting_order'));
    }

    public function test_create_form_shows_use_previous_lineup_button_when_only_a_completed_box_score_exists(): void
    {
        // Entered directly via the completed-game flow — no Lineup rows at all,
        // only PlayerGameStat rows with a batting_order.
        $player = Player::create(['name' => '山田 太郎']);

        $this->post(route('games.store'), $this->validGamePayload([
            'game_date' => '2026-06-26',
            'opponent' => 'Box Score Only Team',
            'player_ids' => [$player->id],
        ]));

        $response = $this->get(route('games.create'));

        $response->assertStatus(200);
        $response->assertSee('前回のスタメンを使う');
    }

    public function test_storing_a_game_saves_player_positions(): void
    {
        $yamada = Player::create(['name' => '山田']);
        $suzuki = Player::create(['name' => '鈴木']);

        $response = $this->post(route('games.store'), $this->validGamePayload([
            'player_ids' => [$yamada->id, $suzuki->id],
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
        $player = Player::create(['name' => '山田']);

        $this->post(route('games.store'), $this->validGamePayload([
            'player_ids' => [$player->id],
            'position' => ['CF'],
            'ab' => [4],
            'h' => [2],
        ]));

        $game = Game::firstOrFail();
        $response = $this->get(route('games.show', $game));

        $response->assertStatus(200);
        $response->assertSee('山田', false);
        $response->assertSee('CF', false);
        $response->assertDontSee('>守備位置<', false);
    }
}
