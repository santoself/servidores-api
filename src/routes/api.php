<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;

Route::middleware('api')->group(function () {
    // Autenticação
    Route::post('login', [AuthController::class, 'login']);
    
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user', [AuthController::class, 'user']);

    // Servidores Efetivos
    Route::get('servidores-efetivos', [ServidorEfetivoController::class, 'index']);
    Route::post('servidores-efetivos', [ServidorEfetivoController::class, 'store']);
    Route::get('servidores-efetivos/{id}', [ServidorEfetivoController::class, 'show']);
    Route::put('servidores-efetivos/{id}', [ServidorEfetivoController::class, 'update']);
    Route::delete('servidores-efetivos/{id}', [ServidorEfetivoController::class, 'destroy']);
    
    // Upload e visualização de fotos
    Route::post('servidores-efetivos/{id}/fotos', [ServidorEfetivoController::class, 'uploadFoto']);
    Route::get('servidores-efetivos/{id}/fotos/{fotoId}', [ServidorEfetivoController::class, 'getFotoUrl']);
    
    // Consultas específicas
    Route::get('unidades/{unidId}/servidores-efetivos', [ServidorEfetivoController::class, 'getByUnidade']);
    Route::get('servidores-efetivos/endereco-funcional', [ServidorEfetivoController::class, 'getEnderecoFuncional']);

    // Servidores Temporários
    Route::get('servidores-temporarios', [ServidorTemporarioController::class, 'index']);
    Route::post('servidores-temporarios', [ServidorTemporarioController::class, 'store']);
    Route::get('servidores-temporarios/{id}', [ServidorTemporarioController::class, 'show']);
    Route::put('servidores-temporarios/{id}', [ServidorTemporarioController::class, 'update']);
    Route::delete('servidores-temporarios/{id}', [ServidorTemporarioController::class, 'destroy']);

    // Unidades
    Route::get('unidades', [UnidadeController::class, 'index']);
    Route::post('unidades', [UnidadeController::class, 'store']);
    Route::get('unidades/{id}', [UnidadeController::class, 'show']);
    Route::put('unidades/{id}', [UnidadeController::class, 'update']);
    Route::delete('unidades/{id}', [UnidadeController::class, 'destroy']);

    // Lotações
    Route::get('lotacoes', [LotacaoController::class, 'index']);
    Route::post('lotacoes', [LotacaoController::class, 'store']);
    Route::get('lotacoes/{id}', [LotacaoController::class, 'show']);
    Route::put('lotacoes/{id}', [LotacaoController::class, 'update']);
    Route::delete('lotacoes/{id}', [LotacaoController::class, 'destroy']);
});