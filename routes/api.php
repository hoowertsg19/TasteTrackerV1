<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\EmpleadoController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\PedidoController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EstadoPedidoController;
use App\Http\Controllers\DashboardController;

Route::prefix('v1')->group(function () {
    // Auth (públicas)
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('auth/check-email', [AuthController::class, 'checkEmail'])
        ->middleware('throttle:10,1');

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

        Route::prefix('dashboard')->group(function () {
            Route::get('stats', [DashboardController::class, 'stats']);
            Route::get('ventas-chart', [DashboardController::class, 'ventasChart']);
            Route::get('pedidos-recientes', [DashboardController::class, 'pedidosRecientes']);
            Route::get('resumen', [DashboardController::class, 'resumen']);
        });
    });
});
