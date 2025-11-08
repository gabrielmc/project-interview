<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;

Route::get('/ping', fn() => response()->json(['status' => 'ok']));
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user()); // Rota para obter usuário autenticado

// Grupo de rotas da API (sem autenticação)
Route::prefix('v1')->group(function () {
    
    // ============================================
    // ROTAS DE USUÁRIOS
    // ============================================
    
    Route::get('/usuarios/pesquisar', [UserController::class, 'search'])->name('api.usuarios.search');
    Route::get('/usuarios/{id}/enderecos', [UserController::class, 'getAddresses'])->name('api.usuarios.enderecos');
    Route::apiResource('usuarios', UserController::class)->names([
        'index' => 'api.usuarios.index',      // GET /api/v1/usuarios - Listar todos
        'store' => 'api.usuarios.store',      // POST /api/v1/usuarios - Criar
        'show' => 'api.usuarios.show',        // GET /api/v1/usuarios/{id} - Detalhar
        'update' => 'api.usuarios.update',    // PUT/PATCH /api/v1/usuarios/{id} - Atualizar
        'destroy' => 'api.usuarios.destroy',  // DELETE /api/v1/usuarios/{id} - Excluir
    ]);
    
    // ============================================
    // ROTAS DE PERFIS
    // ============================================

    Route::get('/perfis/{id}/usuarios', [ProfileController::class, 'getUsers'])->name('api.perfis.usuarios');
    Route::apiResource('perfis', ProfileController::class)->names([
        'index' => 'api.perfis.index',
        'store' => 'api.perfis.store',
        'show' => 'api.perfis.show',
        'update' => 'api.perfis.update',
        'destroy' => 'api.perfis.destroy',
    ]);
    
    // ============================================
    // ROTAS DE ENDEREÇOS
    // ============================================

    Route::get('/enderecos/{id}/usuarios', [AddressController::class, 'getUsers'])->name('api.enderecos.usuarios');
    Route::post('/enderecos/{id}/vincular-usuario', [AddressController::class, 'attachUser'])->name('api.enderecos.vincular'); 
    Route::delete('/enderecos/{id}/desvincular-usuario/{userId}', [AddressController::class, 'detachUser'])->name('api.enderecos.desvincular');
    Route::apiResource('enderecos', AddressController::class)->names([
        'index' => 'api.enderecos.index',
        'store' => 'api.enderecos.store',
        'show' => 'api.enderecos.show',
        'update' => 'api.enderecos.update',
        'destroy' => 'api.enderecos.destroy',
    ]);
    
    // ============================================
    // ROTA DE BUSCA DE CEP (Opcional - Via API externa)
    // ============================================
    Route::get('/cep/{cep}', [AddressController::class, 'searchCep'])->name('api.cep.buscar');
});