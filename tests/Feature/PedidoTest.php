<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_pedidos(): void
    {
        $this->getJson('/api/v1/pedidos')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_pedidos(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $res = $this->getJson('/api/v1/pedidos');
        $res->assertOk();
        // Debe ser un arreglo (posiblemente vacío)
        $this->assertIsArray($res->json());
    }

    public function test_authenticated_user_can_create_pedido(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $cliente = Cliente::create(['nombre_cliente' => 'Ana', 'telefono' => '555-1111', 'direccion' => 'Calle 1']);
        $empleado = Empleado::create(['nombre_completo' => 'Juan Pérez', 'rol' => 'Mesero', 'activo' => true]);
        $menu = Menu::create(['nombre' => 'Hamburguesa', 'precio' => 100.0, 'categoria' => 'comidas', 'disponible' => true]);

        $payload = [
            'id_cliente' => $cliente->id_cliente,
            'id_empleado' => $empleado->id_empleado,
            'numero_mesa' => 'A1',
            'detalles' => [
                [
                    'id_menu' => $menu->id_menu,
                    'cantidad' => 2,
                    'precio_unitario' => 100.0,
                ],
            ],
        ];

        $res = $this->postJson('/api/v1/pedidos', $payload);
        $res->assertStatus(201)
            ->assertJsonStructure([
                'id_pedido', 'id_cliente', 'id_empleado', 'id_estado', 'numero_mesa', 'fecha_creacion', 'total',
                'cliente' => ['id_cliente', 'nombre_cliente'],
                'empleado' => ['id_empleado', 'nombre_completo'],
                'estado' => ['id_estado', 'nombre_estado'],
                'detalles' => [
                    ['id_detalle', 'id_menu', 'cantidad', 'precio_unitario', 'subtotal', 'menu' => ['id_menu', 'nombre']]
                ],
            ]);
    }

    public function test_validation_fails_creating_pedido_with_invalid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $cliente = Cliente::create(['nombre_cliente' => 'Ana', 'telefono' => '555-1111', 'direccion' => 'Calle 1']);
        $empleado = Empleado::create(['nombre_completo' => 'Juan Pérez', 'rol' => 'Mesero', 'activo' => true]);

        $payload = [
            'id_cliente' => $cliente->id_cliente,
            'id_empleado' => $empleado->id_empleado,
            // 'detalles' faltante para provocar 422
        ];

        $res = $this->postJson('/api/v1/pedidos', $payload);
        $res->assertStatus(422)->assertJsonValidationErrors(['detalles']);
    }
}
