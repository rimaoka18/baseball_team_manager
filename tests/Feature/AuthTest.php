<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_reachable_when_no_users_exist(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_registering_the_first_user_logs_them_in(): void
    {
        $response = $this->post(route('register.attempt'), [
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('games.index'));
        $this->assertAuthenticated();
        $this->assertSame(1, User::count());
    }

    public function test_register_page_redirects_to_login_once_a_user_already_exists(): void
    {
        User::factory()->create();

        $response = $this->get(route('register'));

        $response->assertRedirect(route('login'));
    }

    public function test_registering_is_rejected_once_a_user_already_exists(): void
    {
        User::factory()->create();

        $response = $this->post(route('register.attempt'), [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame(1, User::count());
        $this->assertDatabaseMissing('users', ['email' => 'second@example.com']);
    }

    public function test_login_page_hides_the_register_link_once_a_user_already_exists(): void
    {
        User::factory()->create();

        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertDontSee('新規登録');
    }

    public function test_a_registered_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('games.index'));
        $this->assertAuthenticatedAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('games.index'));
        $this->assertGuest();
    }
}
