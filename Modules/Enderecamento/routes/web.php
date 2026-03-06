<?php

use Illuminate\Support\Facades\Route;
use Modules\Enderecamento\Http\Controllers\EnderecoController;

Route::middleware(['auth'])->group(function () {
    Route::get('/enderecamentos', [EnderecoController::class, 'index'])->name('enderecamento.index');
});

Route::middleware(['auth'])->prefix('api/enderecamentos')->group(function () {
    Route::get('/tenants', [EnderecoController::class, 'searchTenants'])->name('enderecamento.tenants.search');
    Route::get('/armazens', [EnderecoController::class, 'searchArmazens'])->name('enderecamento.armazens.search');
    Route::get('/enderecos', [EnderecoController::class, 'searchEnderecos'])->name('enderecamento.enderecos.search');
    Route::get('/layout-fisico', [EnderecoController::class, 'getLayoutFisico'])->name('enderecamento.layout-fisico.api');
    Route::post('/layout-fisico/generate-script', [EnderecoController::class, 'generateLayoutScript'])->name('enderecamento.layout-fisico.generate');
    Route::post('/layout-fisico/preview-nodes', [EnderecoController::class, 'previewNodes'])->name('enderecamento.layout-fisico.preview');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/layout-fisico/{tenantId}/{armazemId}/{enderecamentoId}', [EnderecoController::class, 'layoutFisico'])->name('enderecamento.layout-fisico');
});
