<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Rotas da aplicação web 
*/

// Rota principal - Home do sistema CRUD
Route::get('/', function () {
    return view('app');
})->name('home');

Route::get('/usuarios', function () {
    return view('app');
})->name('usuarios');

Route::get('/perfis', function () {
    return view('app');
})->name('perfis');

Route::get('/enderecos', function () {
    return view('app');
})->name('enderecos');

// Manter as rotas de autenticação comentadas para uso futuro
// Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rota de fallback para SPA (Single Page Application)
// Redireciona qualquer rota não encontrada para o app Vue
Route::fallback(function () {
    return view('app');
});