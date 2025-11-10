<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EstadoPedido;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\EstadoPedidoResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Estados",
 *   description="Catálogo de estados de pedido (solo lectura)"
 * )
 *
 * @OA\Schema(
 *   schema="EstadoPedido",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id_estado", type="integer", format="int64", example=1),
 *     @OA\Property(property="nombre_estado", type="string", example="Recibido")
 *   }
 * )
 */
class EstadoPedidoController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v1/estados-pedido",
     *   tags={"Estados"},
     *   summary="Listar estados de pedido",
     *   description="Devuelve el catálogo de estados de pedido.",
     *   @OA\Response(
     *     response=200,
     *     description="Listado obtenido",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/EstadoPedido"))
     *   )
     * )
     */
    public function index()
    {
        $items = Cache::remember('estados_pedido', 600, function () {
            return EstadoPedido::query()->orderBy('nombre_estado')->get();
        });
        return EstadoPedidoResource::collection($items);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/estados-pedido/{id}",
     *   tags={"Estados"},
     *   summary="Obtener un estado de pedido",
     *   description="Devuelve un estado del catálogo.",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", format="int64")),
     *   @OA\Response(response=200, description="Estado encontrado", @OA\JsonContent(ref="#/components/schemas/EstadoPedido")),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(string $id)
    {
        $estado = EstadoPedido::findOrFail($id);
        return new EstadoPedidoResource($estado);
    }
}
