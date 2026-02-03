<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StatsController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stats/santri', [StatsController::class, 'santri'])->name('stats.santri');

Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');

// Pembayaran Routes
Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
    // Main routes
    Route::get('/', [PembayaranController::class, 'index'])->name('index');
    Route::get('/data', [PembayaranController::class, 'getData'])->name('data');
    Route::post('/', [PembayaranController::class, 'store'])->name('store');
    Route::get('/{id}', [PembayaranController::class, 'show'])->name('show');
    Route::put('/{id}', [PembayaranController::class, 'update'])->name('update');
    Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
    
    // Bulk actions
    Route::post('/bulk-destroy', [PembayaranController::class, 'bulkDestroy'])->name('bulk-destroy');
    
    // Helper routes
    Route::get('/search/santri', [PembayaranController::class, 'searchSantri'])->name('search-santri');
    Route::get('/jenis/{id}', [PembayaranController::class, 'getJenisPembayaran'])->name('jenis-detail');
});