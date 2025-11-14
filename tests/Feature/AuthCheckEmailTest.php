<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthCheckEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_400_when_email_is_not_provided(): void
    {
        $response = $this->getJson('/api/v1/auth/check-email');

        $response->assertStatus(400)
            ->assertJson([
                'exists' => false,
                'message' => 'Email no proporcionado',
            ]);
    }

    public function test_returns_400_when_email_is_invalid(): void
    {
        $response = $this->getJson('/api/v1/auth/check-email?email=not-an-email');

        $response->assertStatus(400)
            ->assertJson([
                'exists' => false,
                'message' => 'Email no proporcionado',
            ]);
    }

    public function test_returns_200_and_exists_true_when_email_is_registered(): void
    {
        $user = User::factory()->create([
            'email' => 'registered@example.com',
        ]);

        $response = $this->getJson('/api/v1/auth/check-email?email=registered@example.com');

        $response->assertStatus(200)
            ->assertJson([
                'exists' => true,
                'message' => 'Email registrado',
            ]);
    }

    public function test_returns_200_and_exists_false_when_email_is_not_registered(): void
    {
        $response = $this->getJson('/api/v1/auth/check-email?email=unknown@example.com');

        $response->assertStatus(200)
            ->assertJson([
                'exists' => false,
                'message' => 'Email no registrado',
            ]);
    }
}
