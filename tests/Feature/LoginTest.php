<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_registred_user_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_not_registred_user_can_not_login(): void
    {

        $response = $this->postJson('/api/v1/login', [
            'email' => fake()->email,
            'password' => fake()->password]);

        $response->assertStatus(422);
        $response->assertJsonMissing(['access_token']);
    }
}
