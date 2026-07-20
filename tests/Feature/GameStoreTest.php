<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\PlayerGameStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameStoreTest extends TestCase
{
    use RefreshDatabase;

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
}
