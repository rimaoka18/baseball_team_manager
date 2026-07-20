<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_players_matching_the_query(): void
    {
        Player::create(['name' => '山田 太郎']);
        Player::create(['name' => '山田 次郎']);
        Player::create(['name' => '鈴木 一郎']);

        $response = $this->get(route('players.autocomplete', ['q' => '山田']));

        $response->assertStatus(200);
        $response->assertJson(['山田 太郎', '山田 次郎']);
        $response->assertJsonMissing(['鈴木 一郎']);
    }

    public function test_it_returns_an_empty_array_for_a_blank_query(): void
    {
        Player::create(['name' => '山田 太郎']);

        $response = $this->get(route('players.autocomplete', ['q' => '']));

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    public function test_it_limits_results_to_eight(): void
    {
        foreach (range(1, 10) as $i) {
            Player::create(['name' => "Batter {$i}"]);
        }

        $response = $this->get(route('players.autocomplete', ['q' => 'Batter']));

        $response->assertStatus(200);
        $this->assertCount(8, $response->json());
    }
}
