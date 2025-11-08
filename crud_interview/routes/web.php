<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Rotas da aplicação web (SPA)
*/

Route::get('/', function () {
    return view('app');
})->name('home');

// Rota de fallback - tudo que não é API vai para o Vue
Route::fallback(function () {
    return view('app');
});