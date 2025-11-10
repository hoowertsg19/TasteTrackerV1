<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmpleadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_empleados(): void
    {
        $this->getJson('/api/v1/empleados')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_empleados(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Empleado::create(['nombre_completo' => 'Juan Pérez', 'rol' => 'Mesero', 'activo' => true]);
        Empleado::create(['nombre_completo' => 'Ana López', 'rol' => 'Cajera', 'activo' => true]);

        $res = $this->getJson('/api/v1/empleados');
        $res->assertOk()->assertJsonCount(2);
    }

    public function test_authenticated_user_can_create_empleado(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'nombre_completo' => 'Carlos Santana',
            'rol' => 'Cocinero',
            'activo' => true,
        ];

        $res = $this->postJson('/api/v1/empleados', $payload);
        $res->assertStatus(201)
            ->assertJsonStructure(['id_empleado', 'nombre_completo', 'rol', 'activo']);

        $this->assertDatabaseHas('empleados', ['nombre_completo' => 'Carlos Santana']);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'nombre_completo' => '',
            'rol' => str_repeat('x', 101),
        ];

        $res = $this->postJson('/api/v1/empleados', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_completo', 'rol']);
    }
}
