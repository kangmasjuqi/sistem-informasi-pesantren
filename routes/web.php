<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\PembayaranController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role'])->group(function () {
    
    // Dashboard (All authenticated users)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
        Route::get('/stats/santri', [StatsController::class, 'santri'])->name('stats.santri');
    });
    
    // Santri Dashboard (Only Santri)
    Route::middleware(['role:SANTRI'])->group(function () {
        Route::get('/santri/dashboard', function () {
            return view('santri.dashboard');
        })->name('santri.dashboard');
    });

    // View Santri Profile (All staff can view)
    Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS,STAFF_TU,BENDAHARA'])->group(function () {
        Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
    });

    Route::middleware(['role:WALI'])->group(function () {
        Route::get('/wali/dashboard', function () {
            return view('wali.dashboard');
        })->name('wali.dashboard');
    });
    
    Route::middleware(['role:SUPERADMIN,ADMIN'])->group(function () {
        // User Management
        Route::get('/users', function () {
            return view('users.index');
        })->name('users.index');

        // Role Management
        Route::get('/roles', function () {
            return view('roles.index');
        })->name('roles.index');
    });
    
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        
        // View Pembayaran (Admin, Bendahara, Staff TU, Kepsek - read only)
        Route::middleware(['role:SUPERADMIN,ADMIN,BENDAHARA,STAFF_TU,KEPSEK'])->group(function () {
            Route::get('/', [PembayaranController::class, 'index'])->name('index');
            Route::get('/data', [PembayaranController::class, 'getData'])->name('data');
            Route::get('/{id}', [PembayaranController::class, 'show'])->name('show');
            Route::get('/search/santri', [PembayaranController::class, 'searchSantri'])->name('search-santri');
            Route::get('/jenis/{id}', [PembayaranController::class, 'getJenisPembayaran'])->name('jenis-detail');
        });

        // Create, Update, Delete Pembayaran (Admin, Bendahara only)
        Route::middleware(['role:SUPERADMIN,ADMIN,BENDAHARA'])->group(function () {
            Route::post('/', [PembayaranController::class, 'store'])->name('store');
            Route::put('/{id}', [PembayaranController::class, 'update'])->name('update');
            Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-destroy', [PembayaranController::class, 'bulkDestroy'])->name('bulk-destroy');
        });
    });

});
