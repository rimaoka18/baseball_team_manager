<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RosterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_roster_page_loads_and_links_to_add_player_page(): void
    {
        $response = $this->get(route('roster.index'));

        $response->assertStatus(200);
        $response->assertSee('選手一覧');
        $response->assertSee('選手を追加');
        $response->assertSee(route('roster.players.create'), false);
    }

    public function test_add_player_page_loads_and_shows_the_form(): void
    {
        $response = $this->get(route('roster.players.create'));

        $response->assertStatus(200);
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

    public function test_adding_a_player_with_jersey_number(): void
    {
        $response = $this->post(route('roster.players.store'), [
            'name' => '今岡 稜',
            'jersey_number' => 18,
        ]);

        $response->assertRedirect(route('roster.index'));
        $this->assertDatabaseHas('players', [
            'name' => '今岡 稜',
            'jersey_number' => 18,
        ]);
    }

    public function test_adding_a_duplicate_jersey_number_is_rejected(): void
    {
        Player::create(['name' => '山田', 'jersey_number' => 18]);

        $response = $this->post(route('roster.players.store'), [
            'name' => '鈴木',
            'jersey_number' => 18,
        ]);

        $response->assertSessionHasErrors('jersey_number');
        $this->assertSame(1, Player::where('jersey_number', 18)->count());
    }

    public function test_adding_a_player_with_jersey_number_00_is_allowed_and_distinct_from_0(): void
    {
        Player::create(['name' => '山田', 'jersey_number' => '0']);

        $response = $this->post(route('roster.players.store'), [
            'name' => '鈴木',
            'jersey_number' => '00',
        ]);

        $response->assertRedirect(route('roster.index'));
        $response->assertSessionDoesntHaveErrors('jersey_number');
        $this->assertDatabaseHas('players', ['name' => '鈴木', 'jersey_number' => '00']);
        $this->assertDatabaseHas('players', ['name' => '山田', 'jersey_number' => '0']);
    }

    public function test_roster_list_defaults_to_ascending_jersey_number_order(): void
    {
        Player::create(['name' => '選手B', 'jersey_number' => 23]);
        Player::create(['name' => '選手A', 'jersey_number' => '00']);
        Player::create(['name' => '選手C', 'jersey_number' => null]);

        $response = $this->get(route('roster.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        $posA = strpos($content, '選手A');
        $posB = strpos($content, '選手B');
        $posC = strpos($content, '選手C');

        $this->assertNotFalse($posA);
        $this->assertNotFalse($posB);
        $this->assertNotFalse($posC);
        $this->assertTrue($posA < $posB);
        $this->assertTrue($posB < $posC);
    }

    public function test_adding_a_player_with_a_photo_stores_it_on_the_photos_disk(): void
    {
        Storage::fake('photos');

        $response = $this->post(route('roster.players.store'), [
            'name' => '山田',
            'photo' => UploadedFile::fake()->image('face.jpg'),
        ]);

        $response->assertRedirect(route('roster.index'));

        $player = Player::where('name', '山田')->firstOrFail();
        $this->assertNotNull($player->photo_path);
        Storage::disk('photos')->assertExists($player->photo_path);
        $this->assertNotNull($player->photoUrl());
    }

    public function test_adding_a_player_with_a_non_image_photo_is_rejected(): void
    {
        $response = $this->post(route('roster.players.store'), [
            'name' => '山田',
            'photo' => UploadedFile::fake()->create('not-a-photo.txt', 10),
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseMissing('players', ['name' => '山田']);
    }

    public function test_player_show_page_displays_stats_card(): void
    {
        $player = Player::create(['name' => '今岡 稜', 'jersey_number' => 18]);

        $response = $this->get(route('roster.players.show', $player));

        $response->assertStatus(200);
        $response->assertSee('#18');
        $response->assertSee('今岡 稜 の成績');
        $response->assertSee('打撃成績');
        $response->assertSee('投手成績');
        $response->assertSee('打率：');
        $response->assertSee('防御率：');
    }
}
