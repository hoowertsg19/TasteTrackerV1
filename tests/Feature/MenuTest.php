<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_menu(): void
    {
        $this->getJson('/api/v1/menu')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_menu(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Menu::create(['nombre' => 'Café', 'precio' => 15.5, 'categoria' => 'bebidas', 'disponible' => true]);
        Menu::create(['nombre' => 'Sándwich', 'precio' => 40.0, 'categoria' => 'comidas', 'disponible' => true]);

        $res = $this->getJson('/api/v1/menu');
        $res->assertOk();
        $res->assertJsonCount(2);
    }

    public function test_authenticated_user_can_create_menu(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'nombre' => 'Té helado',
            'precio' => 25.5,
            'categoria' => 'bebidas',
            'disponible' => true,
        ];

        $res = $this->postJson('/api/v1/menu', $payload);
        $res->assertStatus(201)
            ->assertJsonStructure([
                'id_menu', 'nombre', 'precio', 'categoria', 'disponible', 'imagen_url'
            ]);

        $this->assertDatabaseHas('menu', ['nombre' => 'Té helado']);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'nombre' => '',
            'precio' => -5,
            'categoria' => str_repeat('x', 101),
        ];

        $res = $this->postJson('/api/v1/menu', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'precio', 'categoria']);
    }
}
