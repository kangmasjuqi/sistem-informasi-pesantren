<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KomponenNilaiController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\KategoriInventarisController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniKamarController;

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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
        Route::get('/stats/santri', [StatsController::class, 'santri'])->name('stats.santri');
    });
    
    // Santri Dashboard (Only Santri)
    Route::middleware(['role:SANTRI'])->group(function () {
        Route::get('/santri/dashboard', [SantriController::class, 'dashboard'])->name('santri.dashboard');
        Route::get('/santri/profile', [SantriController::class, 'profile'])->name('santri.profile');
    });

    // View Santri Profile (All staff can view)
    Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS,STAFF_TU,BENDAHARA,WALI,SANTRI'])->group(function () {
        Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
    });

    Route::middleware(['role:WALI'])->group(function () {
        Route::get('/wali/dashboard', [WaliController::class, 'dashboard'])->name('wali.dashboard');
    });
    
    Route::middleware(['role:SUPERADMIN,ADMIN'])->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/data', [UserController::class, 'getData'])->name('data');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            // Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });

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

    Route::prefix('pelajaran')->name('pelajaran.')->group(function () {
        
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get('/', [MataPelajaranController::class, 'index'])->name('index');
            Route::get('/data', [MataPelajaranController::class, 'getData'])->name('data');
            Route::post('/', [MataPelajaranController::class, 'store'])->name('store');
            Route::get('/{id}', [MataPelajaranController::class, 'show'])->name('show');
            Route::put('/{id}', [MataPelajaranController::class, 'update'])->name('update');
            // Route::delete('/{id}', [MataPelajaranController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
        
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
            Route::get('/data', [TahunAjaranController::class, 'getData'])->name('data');
            Route::post('/', [TahunAjaranController::class, 'store'])->name('store');
            Route::get('/{id}', [TahunAjaranController::class, 'show'])->name('show');
            Route::put('/{id}', [TahunAjaranController::class, 'update'])->name('update');
            Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('jenis-pembayaran')->name('jenis-pembayaran.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [JenisPembayaranController::class, 'index'])->name('index');
            Route::get('/data',    [JenisPembayaranController::class, 'getData'])->name('data');
            Route::post('/',       [JenisPembayaranController::class, 'store'])->name('store');
            Route::get('/{id}',    [JenisPembayaranController::class, 'show'])->name('show');
            Route::put('/{id}',    [JenisPembayaranController::class, 'update'])->name('update');
            Route::delete('/{id}', [JenisPembayaranController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('komponen-nilai')->name('komponen-nilai.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get('/',        [KomponenNilaiController::class, 'index'])->name('index');
            Route::get('/data',    [KomponenNilaiController::class, 'getData'])->name('data');
            Route::post('/',       [KomponenNilaiController::class, 'store'])->name('store');
            Route::get('/{id}',    [KomponenNilaiController::class, 'show'])->name('show');
            Route::put('/{id}',    [KomponenNilaiController::class, 'update'])->name('update');
            Route::delete('/{id}', [KomponenNilaiController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('gedung')->name('gedung.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [GedungController::class, 'index'])->name('index');
            Route::get('/data',    [GedungController::class, 'getData'])->name('data');
            Route::post('/',       [GedungController::class, 'store'])->name('store');
            Route::get('/{id}',    [GedungController::class, 'show'])->name('show');
            Route::put('/{id}',    [GedungController::class, 'update'])->name('update');
            Route::delete('/{id}', [GedungController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('kategori-inventaris')->name('kategori-inventaris.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [KategoriInventarisController::class, 'index'])->name('index');
            Route::get('/data',    [KategoriInventarisController::class, 'getData'])->name('data');
            Route::post('/',       [KategoriInventarisController::class, 'store'])->name('store');
            Route::get('/{id}',    [KategoriInventarisController::class, 'show'])->name('show');
            Route::put('/{id}',    [KategoriInventarisController::class, 'update'])->name('update');
            Route::delete('/{id}', [KategoriInventarisController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('perizinan')->name('perizinan.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,WALIKELAS'])->group(function () {
            Route::get('/',              [PerizinanController::class, 'index'])->name('index');
            Route::get('/data',          [PerizinanController::class, 'getData'])->name('data');
            Route::post('/',             [PerizinanController::class, 'store'])->name('store');
            Route::get('/{id}',          [PerizinanController::class, 'show'])->name('show');
            Route::put('/{id}',          [PerizinanController::class, 'update'])->name('update');
            Route::delete('/{id}',       [PerizinanController::class, 'destroy'])->name('destroy');
            Route::get('/search/santri', [PerizinanController::class, 'searchSantri'])->name('search-santri');
        });

        // Approve/reject — stricter role gate
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::post('/{id}/approve', [PerizinanController::class, 'approve'])->name('approve');
            Route::post('/{id}/selesai', [PerizinanController::class, 'selesai'])->name('selesai');
        });
    });

    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [InventarisController::class, 'index'])->name('index');
            Route::get('/data',    [InventarisController::class, 'getData'])->name('data');
            Route::post('/',       [InventarisController::class, 'store'])->name('store');
            Route::get('/{id}',    [InventarisController::class, 'show'])->name('show');
            Route::put('/{id}',    [InventarisController::class, 'update'])->name('update');
            Route::delete('/{id}', [InventarisController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('kamar')->name('kamar.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [KamarController::class, 'index'])->name('index');
            Route::get('/data',    [KamarController::class, 'getData'])->name('data');
            Route::post('/',       [KamarController::class, 'store'])->name('store');
            Route::get('/{id}',    [KamarController::class, 'show'])->name('show');
            Route::put('/{id}',    [KamarController::class, 'update'])->name('update');
            Route::delete('/{id}', [KamarController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('penghuni-kamar')->name('penghuni-kamar.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::get('/',        [PenghuniKamarController::class, 'index'])->name('index');
            Route::get('/data',    [PenghuniKamarController::class, 'getData'])->name('data');
            Route::post('/',       [PenghuniKamarController::class, 'store'])->name('store');
            Route::get('/{id}',    [PenghuniKamarController::class, 'show'])->name('show');
            Route::put('/{id}',    [PenghuniKamarController::class, 'update'])->name('update');
            Route::delete('/{id}', [PenghuniKamarController::class, 'destroy'])->name('destroy');
            Route::get('/search/santri', [PenghuniKamarController::class, 'searchSantri'])->name('search-santri');
        });
    });
});