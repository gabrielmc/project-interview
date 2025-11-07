<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;

// Rota de teste rápida
Route::get('/ping', fn() => response()->json(['status' => 'ok']));

// Rota para obter usuário autenticado
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

// Grupo de rotas da API (sem autenticação)
Route::prefix('v1')->group(function () {
    
    // ============================================
    // ROTAS DE USUÁRIOS
    // ============================================
    
    Route::get('/usuarios/pesquisar', [UserController::class, 'search'])->name('api.usuarios.search');
    
    // Recursos completos de CRUD de usuários
    Route::apiResource('usuarios', UserController::class)->names([
        'index' => 'api.usuarios.index',      // GET /api/v1/usuarios - Listar todos
        'store' => 'api.usuarios.store',      // POST /api/v1/usuarios - Criar
        'show' => 'api.usuarios.show',        // GET /api/v1/usuarios/{id} - Detalhar
        'update' => 'api.usuarios.update',    // PUT/PATCH /api/v1/usuarios/{id} - Atualizar
        'destroy' => 'api.usuarios.destroy',  // DELETE /api/v1/usuarios/{id} - Excluir
    ]);
    
    // Rotas adicionais para usuários
    Route::get('/usuarios/{id}/enderecos', [UserController::class, 'getAddresses'])->name('api.usuarios.enderecos');
    
    
    // ============================================
    // ROTAS DE PERFIS
    // ============================================
    Route::apiResource('perfis', ProfileController::class)->names([
        'index' => 'api.perfis.index',
        'store' => 'api.perfis.store',
        'show' => 'api.perfis.show',
        'update' => 'api.perfis.update',
        'destroy' => 'api.perfis.destroy',
    ]);
    
    // Listar usuários de um perfil específico
    Route::get('/perfis/{id}/usuarios', [ProfileController::class, 'getUsers'])->name('api.perfis.usuarios');
    
    
    // ============================================
    // ROTAS DE ENDEREÇOS
    // ============================================
    Route::apiResource('enderecos', AddressController::class)->names([
        'index' => 'api.enderecos.index',
        'store' => 'api.enderecos.store',
        'show' => 'api.enderecos.show',
        'update' => 'api.enderecos.update',
        'destroy' => 'api.enderecos.destroy',
    ]);
    
    // Vincular endereço a usuário
    Route::post('/enderecos/{id}/vincular-usuario', [AddressController::class, 'attachUser'])->name('api.enderecos.vincular');
    
    // Desvincular endereço de usuário
    Route::delete('/enderecos/{id}/desvincular-usuario/{userId}', [AddressController::class, 'detachUser'])->name('api.enderecos.desvincular');
    
    // Listar usuários de um endereço
    Route::get('/enderecos/{id}/usuarios', [AddressController::class, 'getUsers'])->name('api.enderecos.usuarios');
    
    
    // ============================================
    // ROTA DE BUSCA DE CEP (Opcional - Via API externa)
    // ============================================
    Route::get('/cep/{cep}', [AddressController::class, 'searchCep'])->name('api.cep.buscar');
});