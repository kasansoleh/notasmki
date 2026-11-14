<?php

use App\Http\Controllers\PurchaseProofController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('proofs.index');
});

// Bukti Pembelian Routes
Route::prefix('proofs')->name('proofs.')->group(function () {
    Route::get('/', [PurchaseProofController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseProofController::class, 'create'])->name('create');
    Route::post('/', [PurchaseProofController::class, 'store'])->name('store');
    Route::get('/export/csv', [PurchaseProofController::class, 'exportCsv'])->name('export.csv');
    Route::get('/batch-print', [PurchaseProofController::class, 'batchPrint'])->name('batch-print');
    Route::get('/{proof}', [PurchaseProofController::class, 'show'])->name('show');
    Route::get('/{proof}/edit', [PurchaseProofController::class, 'edit'])->name('edit');
    Route::put('/{proof}', [PurchaseProofController::class, 'update'])->name('update');
    Route::delete('/{proof}', [PurchaseProofController::class, 'destroy'])->name('destroy');
    Route::get('/{proof}/print', [PurchaseProofController::class, 'print'])->name('print');
});

// Kelola Kategori Pengeluaran
Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

// Setting Perusahaan & Kop Surat A4
Route::get('/settings', [CompanySettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [CompanySettingController::class, 'update'])->name('settings.update');
