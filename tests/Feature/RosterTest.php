<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_roster_page_loads_and_shows_add_player_form(): void
    {
        $response = $this->get(route('roster.index'));

        $response->assertStatus(200);
        $response->assertSee('選手');
        $response->assertSee('選手を追加');
        $response->assertSee(route('roster.players.store'), false);
    }

    public function test_legacy_stats_url_redirects_to_roster(): void
    {
        $response = $this->get(route('games.stats'));

        $response->assertRedirect(route('roster.index'));
    }

    public function test_adding_a_player_to_the_roster(): void
    {
        $response = $this->post(route('roster.players.store'), [
            'name' => '山田',
        ]);

        $response->assertRedirect(route('roster.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('players', ['name' => '山田']);
    }

    public function test_adding_a_duplicate_player_name_is_rejected(): void
    {
        Player::create(['name' => '山田']);

        $response = $this->post(route('roster.players.store'), [
            'name' => '山田',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertSame(1, Player::where('name', '山田')->count());
    }

    public function test_roster_lists_players_without_stats(): void
    {
        Player::create(['name' => '今岡']);

        $response = $this->get(route('roster.index'));

        $response->assertStatus(200);
        $response->assertSee('今岡');
        $response->assertSee('選手一覧（1人）');
    }
}
