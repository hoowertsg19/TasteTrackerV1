<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $pedidosHoy = Pedido::whereDate('fecha_creacion', $today)->count();
        $pedidosAyer = Pedido::whereDate('fecha_creacion', $yesterday)->count();
        $pedidosChange = $pedidosAyer > 0
            ? round((($pedidosHoy - $pedidosAyer) / $pedidosAyer) * 100, 1)
            : 0;

        $ventasHoy = Pedido::whereDate('fecha_creacion', $today)->sum('total');
        $ventasAyer = Pedido::whereDate('fecha_creacion', $yesterday)->sum('total');
        $ventasChange = $ventasAyer > 0
            ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1)
            : 0;

        $clientesActivos = Cliente::count();
        $productosMenu = Menu::where('disponible', true)->count();

        return response()->json([
            'pedidos_hoy' => [
                'total' => $pedidosHoy,
                'cambio' => $pedidosChange,
                'tendencia' => $pedidosChange >= 0 ? 'up' : 'down',
            ],
            'ventas_hoy' => [
                'total' => round($ventasHoy, 2),
                'cambio' => $ventasChange,
                'tendencia' => $ventasChange >= 0 ? 'up' : 'down',
            ],
            'clientes_activos' => $clientesActivos,
            'productos_menu' => $productosMenu,
        ], 200);
    }

    public function ventasChart(Request $request)
    {
        $dias = (int) $request->input('dias', 7);
        $dias = min($dias, 365);

        $startDate = Carbon::today()->subDays($dias - 1);

        $ventas = Pedido::select(
            DB::raw('DATE(fecha_creacion) as fecha'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad_pedidos')
            )
            ->where('fecha_creacion', '>=', $startDate)
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $labels = [];
        $data = [];
        $pedidos = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i)->format('Y-m-d');
            $fechaLabel = Carbon::today()->subDays($i)->format('d M');

            $venta = $ventas->firstWhere('fecha', $fecha);

            $labels[] = $fechaLabel;
            $data[] = $venta ? round($venta->total, 2) : 0;
            $pedidos[] = $venta ? $venta->cantidad_pedidos : 0;
        }

        return response()->json([
            'labels' => $labels,
            'ventas' => $data,
            'pedidos' => $pedidos,
        ], 200);
    }

    public function pedidosRecientes(Request $request)
    {
        $limit = (int) $request->input('limit', 5);

        $pedidos = Pedido::with(['cliente', 'estado', 'detalles'])
            ->orderBy('fecha_creacion', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($pedido) {
                return [
                    'id' => $pedido->id_pedido,
                    'numero_pedido' => '#' . str_pad($pedido->id_pedido, 4, '0', STR_PAD_LEFT),
                    'cliente' => [
                        'id' => $pedido->cliente->id,
                        'nombre' => $pedido->cliente->nombre,
                    ],
                    'estado' => [
                        'id' => $pedido->estado->id_estado,
                        'nombre' => $pedido->estado->nombre_estado,
                        'color' => $this->getEstadoColor($pedido->estado->nombre_estado),
                    ],
                    'total' => round($pedido->total, 2),
                    'items' => $pedido->detalles->count(),
                    'fecha' => $pedido->fecha_creacion->format('d/m/Y H:i'),
                    'fecha_relativa' => $pedido->fecha_creacion->diffForHumans(),
                ];
            });

        return response()->json($pedidos, 200);
    }

    public function resumen()
    {
        $today = Carbon::today();

        $pedidosPendientes = Pedido::whereDate('fecha_creacion', $today)
            ->whereHas('estado', fn ($q) => $q->where('nombre_estado', 'Pendiente'))
            ->count();

        $pedidosEnCocina = Pedido::whereDate('fecha_creacion', $today)
            ->whereHas('estado', fn ($q) => $q->where('nombre_estado', 'En cocina'))
            ->count();

        $pedidosListos = Pedido::whereDate('fecha_creacion', $today)
            ->whereHas('estado', fn ($q) => $q->where('nombre_estado', 'Listo'))
            ->count();

        $ticketPromedio = Pedido::whereDate('fecha_creacion', $today)->avg('total');

        return response()->json([
            'pedidos_pendientes' => $pedidosPendientes,
            'pedidos_en_cocina' => $pedidosEnCocina,
            'pedidos_listos' => $pedidosListos,
            'ticket_promedio' => round($ticketPromedio ?? 0, 2),
        ], 200);
    }

    private function getEstadoColor($nombreEstado)
    {
        return match ($nombreEstado) {
            'Pendiente' => 'yellow',
            'En cocina' => 'blue',
            'Listo' => 'green',
            'Entregado' => 'gray',
            'Cancelado' => 'red',
            default => 'gray',
        };
    }
}
