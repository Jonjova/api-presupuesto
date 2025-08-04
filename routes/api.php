<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\EjecucionMensualController;
use App\Http\Controllers\Api\PresupuestoController;
use App\Http\Controllers\Api\ProvisionController;
use App\Http\Controllers\Api\UnidadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('unidades', UnidadController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('presupuestos', PresupuestoController::class);
Route::apiResource('ejecuciones', EjecucionMensualController::class);
Route::apiResource('provisiones', ProvisionController::class);

// Rutas adicionales para funcionalidades específicas
Route::prefix('categoria')->group(function () {
    // Obtener categorías por tipo (ingreso/gasto)
    Route::get('tipo/{type}', [CategoriaController::class, 'byType']);
    
    // Obtener solo categorías padre
    Route::get('principales', [CategoriaController::class, 'index']);
    
    // Obtener árbol completo de categorías
    Route::get('arbol', [CategoriaController::class, 'tree']);

    // Mover subcategorías
    Route::patch('{id}/mover', [CategoriaController::class, 'moveSubcategories']);
});

