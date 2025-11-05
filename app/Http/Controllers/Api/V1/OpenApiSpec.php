<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="TasteTracker API",
 *   version="1.0.0",
 *   description="API REST del restaurante para gestionar menú, pedidos, clientes y empleados."
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Servidor principal"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="sanctum",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization",
 *   description="Usa 'Bearer <token>' para autenticar"
 * )
 */
class OpenApiSpec
{
    // Archivo de anotaciones globales para Swagger/OpenAPI
}
