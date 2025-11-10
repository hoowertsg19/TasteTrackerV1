<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use App\Http\Requests\Pedido\StorePedidoRequest;
use App\Http\Requests\Pedido\UpdatePedidoRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PedidoResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Pedidos",
 *   description="Operaciones para gestionar pedidos y sus detalles"
 * )
 *
 * @OA\Schema(
 *   schema="PedidoDetalleInput",
 *   type="object",
 *   required={"id_menu","cantidad","precio_unitario"},
 *   properties={
 *     @OA\Property(property="id_menu", type="integer", format="int64", example=1),
 *     @OA\Property(property="cantidad", type="integer", minimum=1, example=2),
 *     @OA\Property(property="precio_unitario", type="number", format="float", minimum=0, example=120.00)
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="PedidoCreate",
 *   type="object",
 *   required={"id_cliente","id_empleado","detalles"},
 *   properties={
 *     @OA\Property(property="id_cliente", type="integer", format="int64", example=1),
 *     @OA\Property(property="id_empleado", type="integer", format="int64", example=1),
 *     @OA\Property(property="numero_mesa", type="string", example="A1"),
 *     @OA\Property(property="id_estado", type="integer", format="int64", example=1, description="Opcional, por defecto 'Recibido'"),
 *     @OA\Property(
 *       property="detalles",
 *       type="array",
 *       @OA\Items(ref="#/components/schemas/PedidoDetalleInput")
 *     )
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="PedidoDetalle",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id_detalle", type="integer", format="int64", example=10),
 *     @OA\Property(property="id_pedido", type="integer", format="int64", example=5),
 *     @OA\Property(property="id_menu", type="integer", format="int64", example=1),
 *     @OA\Property(property="cantidad", type="integer", example=2),
 *     @OA\Property(property="precio_unitario", type="number", format="float", example=120.00),
 *     @OA\Property(property="subtotal", type="number", format="float", example=240.00),
 *     @OA\Property(property="menu", ref="#/components/schemas/Menu")
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="Pedido",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id_pedido", type="integer", format="int64", example=5),
 *     @OA\Property(property="id_cliente", type="integer", format="int64", example=1),
 *     @OA\Property(property="id_empleado", type="integer", format="int64", example=1),
 *     @OA\Property(property="id_estado", type="integer", format="int64", example=1),
 *     @OA\Property(property="numero_mesa", type="string", example="A1"),
 *     @OA\Property(property="fecha_creacion", type="string", format="date-time", example="2025-10-15T12:34:56Z"),
 *     @OA\Property(property="total", type="number", format="float", example=240.00),
 *     @OA\Property(property="cliente", ref="#/components/schemas/Cliente"),
 *     @OA\Property(property="empleado", ref="#/components/schemas/Empleado"),
 *     @OA\Property(property="detalles", type="array", @OA\Items(ref="#/components/schemas/PedidoDetalle"))
 *   }
 * )
 */
class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/pedidos",
     *   tags={"Pedidos"},
     *   summary="Listar todos los pedidos",
     *   description="Obtiene todos los pedidos con información del cliente y empleado (sin detalles).",
     *   @OA\Response(
     *     response=200,
     *     description="Listado obtenido correctamente",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Pedido"))
     *   )
     * )
     */
    public function index()
    {
        $items = Pedido::with(['cliente', 'empleado', 'estado', 'detalles.menu'])
            ->orderByDesc('id_pedido')
            ->get();
        return PedidoResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/pedidos",
     *   tags={"Pedidos"},
     *   summary="Crear un nuevo pedido con sus detalles",
     *   description="Crea un pedido y sus detalles en una transacción atómica. Si algún detalle falla, todo se revierte.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/PedidoCreate")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Pedido creado",
     *     @OA\JsonContent(ref="#/components/schemas/Pedido")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StorePedidoRequest $request)
    {
        $validated = $request->validated();

        $pedido = DB::transaction(function () use ($validated) {
            // Determinar estado por defecto 'Recibido' si no se envió
            $estadoId = $validated['id_estado'] ?? null;
            if ($estadoId === null) {
                $estadoId = EstadoPedido::where('nombre_estado', 'Recibido')->value('id_estado');
                if (!$estadoId) {
                    $estadoId = EstadoPedido::create(['nombre_estado' => 'Recibido'])->id_estado;
                }
            }

            // Crear el pedido inicial con total 0; fecha_creacion usa default de DB
            $pedido = Pedido::create([
                'id_cliente' => $validated['id_cliente'],
                'id_empleado' => $validated['id_empleado'],
                'id_estado' => $estadoId,
                'numero_mesa' => $validated['numero_mesa'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($validated['detalles'] as $det) {
                $subtotal = (float)$det['cantidad'] * (float)$det['precio_unitario'];
                $total += $subtotal;

                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_menu' => $det['id_menu'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }

            $pedido->total = $total;
            $pedido->save();

            return $pedido;
        });

        $pedido->load(['cliente', 'empleado', 'estado', 'detalles.menu']);
        return (new PedidoResource($pedido))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/pedidos/{pedido}",
     *   tags={"Pedidos"},
     *   summary="Obtener un pedido con sus relaciones",
     *   description="Devuelve un pedido con cliente, empleado y detalles (incluyendo el menú de cada detalle).",
     *   @OA\Parameter(
     *     name="pedido",
     *     in="path",
     *     description="ID del pedido",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Pedido encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/Pedido")
     *   ),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'empleado', 'estado', 'detalles.menu']);
        return new PedidoResource($pedido);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/pedidos/{pedido}",
     *   tags={"Pedidos"},
     *   summary="Actualizar el estado de un pedido",
     *   description="Actualiza únicamente el estado del pedido (id_estado).",
     *   @OA\Parameter(
     *     name="pedido",
     *     in="path",
     *     description="ID del pedido",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(type="object", required={"id_estado"}, @OA\Property(property="id_estado", type="integer", format="int64", example=2))
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Pedido actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Pedido")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     *
     * @OA\Patch(
     *   path="/api/v1/pedidos/{pedido}",
     *   tags={"Pedidos"},
     *   summary="Actualizar parcialmente un pedido (estado)",
     *   description="Actualiza el estado del pedido (id_estado).",
     *   @OA\Parameter(
     *     name="pedido",
     *     in="path",
     *     description="ID del pedido",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(type="object", required={"id_estado"}, @OA\Property(property="id_estado", type="integer", format="int64", example=3))
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Pedido actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Pedido")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $validated = $request->validated();

        $pedido->id_estado = $validated['id_estado'];
        $pedido->save();

        $pedido->load(['cliente', 'empleado', 'estado', 'detalles.menu']);
        return new PedidoResource($pedido);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/pedidos/{pedido}",
     *   tags={"Pedidos"},
     *   summary="Eliminar un pedido",
     *   description="Elimina un pedido. Los detalles asociados se eliminan en cascada.",
     *   @OA\Parameter(
     *     name="pedido",
     *     in="path",
     *     description="ID del pedido",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
