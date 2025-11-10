<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Http\Resources\EmpleadoResource;
use App\Http\Requests\Empleado\StoreEmpleadoRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoRequest;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Empleados",
 *   description="Operaciones CRUD para la gestión de empleados"
 * )
 *
 * @OA\Schema(
 *   schema="Empleado",
 *   type="object",
 *   required={"nombre_completo","rol"},
 *   properties={
 *     @OA\Property(property="id_empleado", type="integer", format="int64", example=1),
 *     @OA\Property(property="nombre_completo", type="string", example="Juan Pérez"),
 *     @OA\Property(property="rol", type="string", example="Mesero"),
 *     @OA\Property(property="activo", type="boolean", example=true)
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="EmpleadoCreate",
 *   type="object",
 *   required={"nombre_completo","rol"},
 *   properties={
 *     @OA\Property(property="nombre_completo", type="string", maxLength=255, example="Juan Pérez"),
 *     @OA\Property(property="rol", type="string", maxLength=100, example="Mesero"),
 *     @OA\Property(property="activo", type="boolean", example=true)
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="EmpleadoUpdate",
 *   type="object",
 *   properties={
 *     @OA\Property(property="nombre_completo", type="string", maxLength=255, example="Juan P. Pérez"),
 *     @OA\Property(property="rol", type="string", maxLength=100, example="Cajero"),
 *     @OA\Property(property="activo", type="boolean", example=false)
 *   }
 * )
 */
class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/empleados",
     *   tags={"Empleados"},
     *   summary="Listar todos los empleados",
     *   description="Obtiene el listado completo de empleados ordenado por nombre.",
     *   @OA\Response(
     *     response=200,
     *     description="Listado obtenido correctamente",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empleado"))
     *   )
     * )
     */
    public function index()
    {
        $items = Empleado::query()->orderBy('nombre_completo')->get();
        return EmpleadoResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/empleados",
     *   tags={"Empleados"},
     *   summary="Crear un nuevo empleado",
     *   description="Crea un nuevo registro de empleado.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/EmpleadoCreate")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Empleado creado",
     *     @OA\JsonContent(ref="#/components/schemas/Empleado")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StoreEmpleadoRequest $request)
    {
        $validated = $request->validated();

        $empleado = Empleado::create([
            'nombre_completo' => $validated['nombre_completo'],
            'rol' => $validated['rol'],
            'activo' => $validated['activo'] ?? true,
        ]);

        return (new EmpleadoResource($empleado))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/empleados/{empleado}",
     *   tags={"Empleados"},
     *   summary="Obtener un empleado",
     *   description="Devuelve los detalles de un empleado específico.",
     *   @OA\Parameter(
     *     name="empleado",
     *     in="path",
     *     description="ID del empleado",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Empleado encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/Empleado")
     *   ),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(string $id)
    {
        $empleado = Empleado::findOrFail($id);
        return new EmpleadoResource($empleado);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/empleados/{empleado}",
     *   tags={"Empleados"},
     *   summary="Actualizar un empleado",
     *   description="Actualiza los datos de un empleado.",
     *   @OA\Parameter(
     *     name="empleado",
     *     in="path",
     *     description="ID del empleado",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/EmpleadoUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Empleado actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Empleado")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     *
     * @OA\Patch(
     *   path="/api/v1/empleados/{empleado}",
     *   tags={"Empleados"},
     *   summary="Actualizar parcialmente un empleado",
     *   description="Actualiza uno o más campos de un empleado.",
     *   @OA\Parameter(
     *     name="empleado",
     *     in="path",
     *     description="ID del empleado",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/EmpleadoUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Empleado actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Empleado")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(UpdateEmpleadoRequest $request, string $id)
    {
        $validated = $request->validated();

        $empleado = Empleado::findOrFail($id);
        $empleado->fill($validated);
        $empleado->save();

        return new EmpleadoResource($empleado);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/empleados/{empleado}",
     *   tags={"Empleados"},
     *   summary="Eliminar un empleado",
     *   description="Elimina un registro de empleado.",
     *   @OA\Parameter(
     *     name="empleado",
     *     in="path",
     *     description="ID del empleado",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
