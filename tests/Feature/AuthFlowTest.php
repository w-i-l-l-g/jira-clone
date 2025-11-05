<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_me()
    {
        $this->getJson('/api/me')->assertStatus(401);
    }

    #[Test]
    public function can_login_via_session_and_hit_me()
    {
        // Make a user with known creds
        $user = User::factory()->create([
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        // 1) Get CSRF cookie (what a browser would do)
        $resp = $this->get('/sanctum/csrf-cookie');
        $xsrf = urldecode($resp->getCookie('XSRF-TOKEN')->getValue());

        // 2) POST /login with CSRF header + session cookie
        $this->withHeader('X-XSRF-TOKEN', $xsrf)
             ->withCookie('XSRF-TOKEN', $xsrf) // mirror browser
             ->post('/login', [
                 'email' => 'demo@example.com',
                 'password' => 'password',
             ])
             ->assertStatus(302)
             ->assertRedirect();

        // 3) Now authenticated, /api/me should return the user
        $this->getJson('/api/me')
             ->assertOk()
             ->assertJsonPath('id', $user->id)
             ->assertJsonPath('email', 'demo@example.com');
    }

    #[Test]
    public function acting_as_user_returns_me()
    {
        $user = User::factory()->create();
        $this->actingAs($user)  // session guard
             ->getJson('/api/me')
             ->assertOk()
             ->assertJsonPath('id', $user->id);
    }

    #[Test]
    public function invalid_login_fails_with_json()
    {
        // Ask for JSON so Laravel won’t HTML-redirect
        $this->withHeader('Accept', 'application/json')
             ->post('/login', [
                 'email' => 'nobody@example.com',
                 'password' => 'bad',
             ])
             ->assertStatus(422);
    }
}