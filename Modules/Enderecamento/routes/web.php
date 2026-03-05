<?php

use Illuminate\Support\Facades\Route;
use Modules\Enderecamento\Http\Controllers\EnderecoController;

Route::middleware(['auth'])->group(function () {
    Route::get('/enderecamentos', [EnderecoController::class, 'index'])->name('enderecamento.index');
});

Route::middleware(['auth'])->prefix('api/enderecamentos')->group(function () {
    Route::get('/tenants', [EnderecoController::class, 'searchTenants'])->name('enderecamento.tenants.search');
    Route::get('/armazens', [EnderecoController::class, 'searchArmazens'])->name('enderecamento.armazens.search');
});
