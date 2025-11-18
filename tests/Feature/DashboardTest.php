<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(401);
    }

    public function test_dashboard_stats_returns_correct_structure(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'pedidos_hoy' => ['total', 'cambio', 'tendencia'],
                'ventas_hoy' => ['total', 'cambio', 'tendencia'],
                'clientes_activos',
                'productos_menu',
            ]);
    }

    public function test_ventas_chart_returns_7_days_by_default(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/dashboard/ventas-chart');

        $response->assertStatus(200);
        $this->assertCount(7, $response->json('labels'));
        $this->assertCount(7, $response->json('ventas'));
        $this->assertCount(7, $response->json('pedidos'));
    }
}
