<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\EmpleadoController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\PedidoController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EstadoPedidoController;

Route::prefix('v1')->group(function () {
    // Auth (públicas)
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Estados de pedido (solo lectura, públicas)
    Route::get('estados-pedido', [EstadoPedidoController::class, 'index']);
    Route::get('estados-pedido/{id}', [EstadoPedidoController::class, 'show']);

    // Rutas protegidas por Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('menu', MenuController::class);
        // Subida de imagen para un ítem del menú (multipart/form-data)
        Route::post('menu/{menu}/imagen', [MenuController::class, 'subirImagen']);
        Route::apiResource('empleados', EmpleadoController::class);
        Route::apiResource('clientes', ClienteController::class);
        Route::apiResource('pedidos', PedidoController::class);
    });
});
