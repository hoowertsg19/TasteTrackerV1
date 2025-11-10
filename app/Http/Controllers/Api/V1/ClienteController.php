<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Resources\ClienteResource;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Clientes",
 *   description="Operaciones CRUD para la gestión de clientes"
 * )
 *
 * @OA\Schema(
 *   schema="Cliente",
 *   type="object",
 *   required={"nombre_cliente"},
 *   properties={
 *     @OA\Property(property="id_cliente", type="integer", format="int64", example=1),
 *     @OA\Property(property="nombre_cliente", type="string", example="Ana López"),
 *     @OA\Property(property="telefono", type="string", example="555-1234"),
 *     @OA\Property(property="direccion", type="string", example="Calle 1 #123, Col. Centro")
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="ClienteCreate",
 *   type="object",
 *   required={"nombre_cliente"},
 *   properties={
 *     @OA\Property(property="nombre_cliente", type="string", maxLength=255, example="Ana López"),
 *     @OA\Property(property="telefono", type="string", maxLength=30, example="555-1234"),
 *     @OA\Property(property="direccion", type="string", maxLength=255, example="Calle 1 #123, Col. Centro")
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="ClienteUpdate",
 *   type="object",
 *   properties={
 *     @OA\Property(property="nombre_cliente", type="string", maxLength=255, example="Ana M. López"),
 *     @OA\Property(property="telefono", type="string", maxLength=30, example="555-9876"),
 *     @OA\Property(property="direccion", type="string", maxLength=255, example="Av. Reforma 456")
 *   }
 * )
 */
class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/clientes",
     *   tags={"Clientes"},
     *   summary="Listar todos los clientes",
     *   description="Obtiene el listado completo de clientes ordenado por nombre.",
     *   @OA\Response(
     *     response=200,
     *     description="Listado obtenido correctamente",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Cliente"))
     *   )
     * )
     */
    public function index()
    {
        $items = Cliente::query()->orderBy('nombre_cliente')->get();
        return ClienteResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/clientes",
     *   tags={"Clientes"},
     *   summary="Crear un nuevo cliente",
     *   description="Crea un nuevo registro de cliente.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/ClienteCreate")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Cliente creado",
     *     @OA\JsonContent(ref="#/components/schemas/Cliente")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StoreClienteRequest $request)
    {
        $validated = $request->validated();

        $cliente = Cliente::create($validated);
        return (new ClienteResource($cliente))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/clientes/{cliente}",
     *   tags={"Clientes"},
     *   summary="Obtener un cliente",
     *   description="Devuelve los detalles de un cliente específico.",
     *   @OA\Parameter(
     *     name="cliente",
     *     in="path",
     *     description="ID del cliente",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cliente encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/Cliente")
     *   ),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return new ClienteResource($cliente);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/clientes/{cliente}",
     *   tags={"Clientes"},
     *   summary="Actualizar un cliente",
     *   description="Actualiza los datos de un cliente.",
     *   @OA\Parameter(
     *     name="cliente",
     *     in="path",
     *     description="ID del cliente",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/ClienteUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cliente actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Cliente")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     *
     * @OA\Patch(
     *   path="/api/v1/clientes/{cliente}",
     *   tags={"Clientes"},
     *   summary="Actualizar parcialmente un cliente",
     *   description="Actualiza uno o más campos de un cliente.",
     *   @OA\Parameter(
     *     name="cliente",
     *     in="path",
     *     description="ID del cliente",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/ClienteUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cliente actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Cliente")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(UpdateClienteRequest $request, string $id)
    {
        $validated = $request->validated();

        $cliente = Cliente::findOrFail($id);
        $cliente->fill($validated);
        $cliente->save();

        return new ClienteResource($cliente);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/clientes/{cliente}",
     *   tags={"Clientes"},
     *   summary="Eliminar un cliente",
     *   description="Elimina un registro de cliente.",
     *   @OA\Parameter(
     *     name="cliente",
     *     in="path",
     *     description="ID del cliente",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
