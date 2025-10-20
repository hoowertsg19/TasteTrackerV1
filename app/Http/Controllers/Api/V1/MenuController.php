<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Menu",
 *   description="Operaciones CRUD para el catálogo de menú"
 * )
 *
 * @OA\Schema(
 *   schema="Menu",
 *   type="object",
 *   required={"nombre","precio","categoria"},
 *   properties={
 *     @OA\Property(property="id_menu", type="integer", format="int64", example=1),
 *     @OA\Property(property="nombre", type="string", example="Hamburguesa Clásica"),
 *     @OA\Property(property="precio", type="number", format="float", example=120.00),
 *     @OA\Property(property="categoria", type="string", example="comidas"),
 *     @OA\Property(property="disponible", type="boolean", example=true)
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="MenuCreate",
 *   type="object",
 *   required={"nombre","precio","categoria"},
 *   properties={
 *     @OA\Property(property="nombre", type="string", maxLength=255, example="Té helado"),
 *     @OA\Property(property="precio", type="number", format="float", minimum=0, example=25.5),
 *     @OA\Property(property="categoria", type="string", maxLength=100, example="bebidas"),
 *     @OA\Property(property="disponible", type="boolean", example=true)
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="MenuUpdate",
 *   type="object",
 *   properties={
 *     @OA\Property(property="nombre", type="string", maxLength=255, example="Té helado grande"),
 *     @OA\Property(property="precio", type="number", format="float", minimum=0, example=29.0),
 *     @OA\Property(property="categoria", type="string", maxLength=100, example="bebidas"),
 *     @OA\Property(property="disponible", type="boolean", example=false)
 *   }
 * )
 */
class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/menu",
     *   tags={"Menu"},
     *   summary="Listar todos los productos del menú",
     *   description="Obtiene una lista completa del catálogo de menú, ordenada por nombre.",
     *   @OA\Response(
     *     response=200,
     *     description="Listado obtenido correctamente",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Menu"))
     *   )
     * )
     */
    public function index()
    {
        $items = Menu::query()->orderBy('nombre')->get();
        return response()->json($items, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/menu",
     *   tags={"Menu"},
     *   summary="Crear un nuevo producto del menú",
     *   description="Crea un producto en el catálogo del menú.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/MenuCreate")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Producto creado",
     *     @OA\JsonContent(ref="#/components/schemas/Menu")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'categoria' => ['required', 'string', 'max:100'],
            'disponible' => ['sometimes', 'boolean'],
        ]);

        $menu = Menu::create([
            'nombre' => $validated['nombre'],
            'precio' => $validated['precio'],
            'categoria' => $validated['categoria'],
            'disponible' => $validated['disponible'] ?? true,
        ]);

        return response()->json($menu, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/menu/{menu}",
     *   tags={"Menu"},
     *   summary="Obtener un producto del menú",
     *   description="Devuelve los detalles de un producto específico del menú.",
     *   @OA\Parameter(
     *     name="menu",
     *     in="path",
     *     description="ID del producto del menú",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Producto encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/Menu")
     *   ),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(Menu $menu)
    {
        return response()->json($menu, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/menu/{menu}",
     *   tags={"Menu"},
     *   summary="Actualizar un producto del menú",
     *   description="Actualiza campos de un producto del menú.",
     *   @OA\Parameter(
     *     name="menu",
     *     in="path",
     *     description="ID del producto del menú",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/MenuUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Producto actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Menu")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     *
     * @OA\Patch(
     *   path="/api/v1/menu/{menu}",
     *   tags={"Menu"},
     *   summary="Actualizar parcialmente un producto del menú",
     *   description="Actualiza uno o más campos de un producto del menú.",
     *   @OA\Parameter(
     *     name="menu",
     *     in="path",
     *     description="ID del producto del menú",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/MenuUpdate")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Producto actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/Menu")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'categoria' => ['sometimes', 'required', 'string', 'max:100'],
            'disponible' => ['sometimes', 'boolean'],
        ]);

        $menu->fill($validated);
        // Si "disponible" viene como null explícito, no lo sobrescribimos
        $menu->save();

        return response()->json($menu, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/menu/{menu}",
     *   tags={"Menu"},
     *   summary="Eliminar un producto del menú",
     *   description="Elimina un producto del catálogo de menú.",
     *   @OA\Parameter(
     *     name="menu",
     *     in="path",
     *     description="ID del producto del menú",
     *     required=true,
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
