<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_roster_row_links_to_the_players_show_page(): void
    {
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->get(route('roster.index'));

        $response->assertStatus(200);
        $response->assertSee(route('roster.players.show', $player), false);
    }

    public function test_show_page_displays_the_players_name_and_jersey_number(): void
    {
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->get(route('roster.players.show', $player));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('7');
    }

    public function test_edit_page_pre_fills_the_current_name_and_jersey_number(): void
    {
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->get(route('roster.players.edit', $player));

        $response->assertStatus(200);
        $response->assertSee('value="山田 太郎"', false);
        $response->assertSee('value="7"', false);
    }

    public function test_updating_a_player_changes_name_and_jersey_number(): void
    {
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->put(route('roster.players.update', $player), [
            'name' => '山田 次郎',
            'jersey_number' => 9,
        ]);

        $response->assertRedirect(route('roster.players.show', $player));
        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'name' => '山田 次郎',
            'jersey_number' => 9,
        ]);
    }

    public function test_updating_a_player_without_changing_name_or_jersey_number_is_allowed(): void
    {
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->put(route('roster.players.update', $player), [
            'name' => '山田 太郎',
            'jersey_number' => 7,
        ]);

        $response->assertRedirect(route('roster.players.show', $player));
        $response->assertSessionHasNoErrors();
    }

    public function test_updating_a_player_to_a_name_used_by_another_player_is_rejected(): void
    {
        Player::create(['name' => '鈴木 一郎']);
        $player = Player::create(['name' => '山田 太郎']);

        $response = $this->put(route('roster.players.update', $player), [
            'name' => '鈴木 一郎',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('players', ['id' => $player->id, 'name' => '山田 太郎']);
    }

    public function test_updating_a_player_to_a_jersey_number_used_by_another_player_is_rejected(): void
    {
        Player::create(['name' => '鈴木 一郎', 'jersey_number' => 18]);
        $player = Player::create(['name' => '山田 太郎', 'jersey_number' => 7]);

        $response = $this->put(route('roster.players.update', $player), [
            'name' => '山田 太郎',
            'jersey_number' => 18,
        ]);

        $response->assertSessionHasErrors('jersey_number');
        $this->assertDatabaseHas('players', ['id' => $player->id, 'jersey_number' => 7]);
    }
}
