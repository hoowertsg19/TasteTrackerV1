<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\EmpleadoController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\PedidoController;

Route::prefix('v1')->group(function () {
    Route::apiResource('menu', MenuController::class);
    Route::apiResource('empleados', EmpleadoController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('pedidos', PedidoController::class);
});
