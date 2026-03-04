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
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\KelasController;

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

/*
|--------------------------------------------------------------------------
| Web Routes — Role Reference
|--------------------------------------------------------------------------
| SUPERADMIN  Full system access
| ADMIN       General pesantren admin
| KEPSEK      Kepala Sekolah / Mudir
| PENGAJAR    Ustadz / Ustadzah (teacher)
| WALIKELAS   Wali Kelas (homeroom teacher)
| STAFF_TU    Staff Tata Usaha
| BENDAHARA   Bendahara keuangan
| SANTRI      Student (own portal)
| WALI        Wali Santri / parent (own portal)
*/

Route::middleware(['auth', 'role'])->group(function () {

    // ── Public staff dashboard (all non-student, non-wali roles) ──────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Santri portal ─────────────────────────────────────────────────────
    Route::middleware(['role:SANTRI'])->group(function () {
        Route::get('/santri/dashboard', [SantriController::class, 'dashboard'])->name('santri.dashboard');
        Route::get('/santri/profile',   [SantriController::class, 'profile'])->name('santri.profile');
    });

    // ── Wali portal ───────────────────────────────────────────────────────
    Route::middleware(['role:WALI'])->group(function () {
        Route::get('/wali/dashboard', [WaliController::class, 'dashboard'])->name('wali.dashboard');
    });

    // =========================================================================
    // AKADEMIK
    // =========================================================================

    // ── Statistik Santri ──────────────────────────────────────────────────
    Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
        Route::get('/stats/santri', [StatsController::class, 'santri'])->name('stats.santri');
    });

    // ── Tahun Ajaran — teaching staff reference this for scheduling ───────
    Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get('/',        [TahunAjaranController::class, 'index'])->name('index');
            Route::get('/data',    [TahunAjaranController::class, 'getData'])->name('data');
            Route::post('/',       [TahunAjaranController::class, 'store'])->name('store');
            Route::get('/{id}',    [TahunAjaranController::class, 'show'])->name('show');
            Route::put('/{id}',    [TahunAjaranController::class, 'update'])->name('update');
            Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Pelajaran — teachers manage their own subjects ────────────────────
    Route::prefix('pelajaran')->name('pelajaran.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get('/',        [MataPelajaranController::class, 'index'])->name('index');
            Route::get('/data',    [MataPelajaranController::class, 'getData'])->name('data');
            Route::post('/',       [MataPelajaranController::class, 'store'])->name('store');
            Route::get('/{id}',    [MataPelajaranController::class, 'show'])->name('show');
            Route::put('/{id}',    [MataPelajaranController::class, 'update'])->name('update');
        });
    });

    // =========================================================================
    // OPERASIONAL
    // =========================================================================

    // ── Pembayaran ────────────────────────────────────────────────────────
    // View: finance, admin, principal, TU all need visibility
    // CUD:  only BENDAHARA + ADMIN layer to prevent unauthorised edits
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU,BENDAHARA'])->group(function () {
            Route::get('/',              [PembayaranController::class, 'index'])->name('index');
            Route::get('/data',          [PembayaranController::class, 'getData'])->name('data');
            Route::get('/{id}',          [PembayaranController::class, 'show'])->name('show');
            Route::get('/search/santri', [PembayaranController::class, 'searchSantri'])->name('search-santri');
            Route::get('/jenis/{id}',    [PembayaranController::class, 'getJenisPembayaran'])->name('jenis-detail');
        });

        Route::middleware(['role:SUPERADMIN,ADMIN,BENDAHARA'])->group(function () {
            Route::post('/',             [PembayaranController::class, 'store'])->name('store');
            Route::put('/{id}',          [PembayaranController::class, 'update'])->name('update');
            Route::delete('/{id}',       [PembayaranController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-destroy', [PembayaranController::class, 'bulkDestroy'])->name('bulk-destroy');
        });
    });

    // ── Perizinan — leave/permission managed by admin & homeroom ─────────
    // Approve/reject gated to KEPSEK + admin layer (not WALIKELAS)
    Route::prefix('perizinan')->name('perizinan.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,WALIKELAS'])->group(function () {
            Route::get('/',              [PerizinanController::class, 'index'])->name('index');
            Route::get('/data',          [PerizinanController::class, 'getData'])->name('data');
            Route::get('/search/santri', [PerizinanController::class, 'searchSantri'])->name('search-santri');
            Route::post('/',             [PerizinanController::class, 'store'])->name('store');
            Route::get('/{id}',          [PerizinanController::class, 'show'])->name('show');
            Route::put('/{id}',          [PerizinanController::class, 'update'])->name('update');
            Route::delete('/{id}',       [PerizinanController::class, 'destroy'])->name('destroy');
        });

        // Final approval/rejection authority — principal + admins only
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK'])->group(function () {
            Route::post('/{id}/approve', [PerizinanController::class, 'approve'])->name('approve');
            Route::post('/{id}/selesai', [PerizinanController::class, 'selesai'])->name('selesai');
        });
    });

    // ── Inventaris — STAFF_TU is the physical asset custodian ────────────
    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::get('/',        [InventarisController::class, 'index'])->name('index');
            Route::get('/data',    [InventarisController::class, 'getData'])->name('data');
            Route::post('/',       [InventarisController::class, 'store'])->name('store');
            Route::get('/{id}',    [InventarisController::class, 'show'])->name('show');
            Route::put('/{id}',    [InventarisController::class, 'update'])->name('update');
            Route::delete('/{id}', [InventarisController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Kamar — STAFF_TU manages dormitory rooms ──────────────────────────
    Route::prefix('kamar')->name('kamar.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::get('/',        [KamarController::class, 'index'])->name('index');
            Route::get('/data',    [KamarController::class, 'getData'])->name('data');
            Route::post('/',       [KamarController::class, 'store'])->name('store');
            Route::get('/{id}',    [KamarController::class, 'show'])->name('show');
            Route::put('/{id}',    [KamarController::class, 'update'])->name('update');
            Route::delete('/{id}', [KamarController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Penghuni Kamar — same scope as Kamar ─────────────────────────────
    Route::prefix('penghuni-kamar')->name('penghuni-kamar.')->group(function () {
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::get('/',              [PenghuniKamarController::class, 'index'])->name('index');
            Route::get('/data',          [PenghuniKamarController::class, 'getData'])->name('data');
            Route::get('/search/santri', [PenghuniKamarController::class, 'searchSantri'])->name('search-santri');
            Route::post('/',             [PenghuniKamarController::class, 'store'])->name('store');
            Route::get('/{id}',          [PenghuniKamarController::class, 'show'])->name('show');
            Route::put('/{id}',          [PenghuniKamarController::class, 'update'])->name('update');
            Route::delete('/{id}',       [PenghuniKamarController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Users — account management, strictly SA + Admin ───────────────────
    Route::middleware(['role:SUPERADMIN,ADMIN'])->group(function () {

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                    [UserController::class, 'index'])->name('index');
            Route::get('/data',                [UserController::class, 'getData'])->name('data');
            Route::post('/',                   [UserController::class, 'store'])->name('store');
            Route::get('/{id}',                [UserController::class, 'show'])->name('show');
            Route::put('/{id}',                [UserController::class, 'update'])->name('update');
            Route::post('/{id}/reset-password',[UserController::class, 'resetPassword'])->name('reset-password');
        });

        Route::get('/roles', fn () => view('roles.index'))->name('roles.index');
    });

    // =========================================================================
    // MASTER DATA
    // =========================================================================

    // ── Komponen Nilai — grade components configured by teaching staff ─────
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

    // ── Jenis Pembayaran — KEPSEK oversees fee structures ─────────────────
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

    // ── Kategori Inventaris — KEPSEK oversees asset taxonomy ──────────────
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

    // ── Gedung — KEPSEK oversees facility master data ─────────────────────
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

    // ── Santri ────────────────────────────────────────────────────────────────
    Route::prefix('santri')->name('santri.')->group(function () {

        // All staff can view student profiles
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS,STAFF_TU,BENDAHARA'])->group(function () {
            Route::get('/',              [SantriController::class, 'index'])->name('index');
            Route::get('/data',          [SantriController::class, 'getData'])->name('data');
            Route::get('/search/santri', [SantriController::class, 'searchSantri'])->name('search-santri');
        });

        // ── View Santri profile (all authenticated roles can view a student) ──
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS,STAFF_TU,BENDAHARA,WALI,SANTRI'])->group(function () {
            Route::get('/{id}/detail',  [SantriController::class, 'showDetail'])->name('show-detail');
            Route::get('/{id}',         [SantriController::class, 'show'])->name('show');
        });

        // CUD restricted to admin layer
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::post('/',             [SantriController::class, 'store'])->name('store');
            Route::put('/{id}',          [SantriController::class, 'update'])->name('update');
            Route::delete('/{id}',       [SantriController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Pengajar ──────────────────────────────────────────────────────────────
    Route::prefix('pengajar')->name('pengajar.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::get('/',        [PengajarController::class, 'index'])->name('index');
            Route::get('/data',    [PengajarController::class, 'getData'])->name('data');
            Route::post('/',       [PengajarController::class, 'store'])->name('store');
            Route::get('/{id}',    [PengajarController::class, 'show'])->name('show');
            Route::put('/{id}',    [PengajarController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengajarController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Kelas ──────────────────────────────────────────────────────────────
    Route::prefix('kelas')->name('kelas.')->group(function () {

        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,PENGAJAR,WALIKELAS'])->group(function () {
            Route::get ('/{kelas}/santri',                    [KelasController::class, 'santri'])->name('santri');
            Route::get ('/{kelas}/santri/data',               [KelasController::class, 'santriData'])->name('santri.data');
            Route::post('/{kelas}/santri',                    [KelasController::class, 'santriStore'])->name('santri.store');
            Route::patch('/{kelas}/santri/{ksId}/exit',       [KelasController::class, 'santriExit'])->name('santri.exit');
            Route::get ('/{kelas}/santri/available',          [KelasController::class, 'santriAvailable'])->name('santri.available');
            Route::get('/search/tahunajaran', [KelasController::class, 'searchTahunAjaran'])->name('search-tahun-ajaran');
            Route::get('/search/aktif-tahun-ajaran', [KelasController::class, 'getAktifTahunAjaran'])->name('get-aktif-tahun-ajaran');
            Route::get('/search/pengajar', [KelasController::class, 'searchPengajar'])->name('search-pengajar');
            Route::get('/',        [KelasController::class, 'index'])->name('index');
            Route::get('/data',    [KelasController::class, 'getData'])->name('data');
            Route::post('/',       [KelasController::class, 'store'])->name('store');
            Route::get('/{id}',    [KelasController::class, 'show'])->name('show');
            Route::put('/{id}',    [KelasController::class, 'update'])->name('update');
            Route::delete('/{id}', [KelasController::class, 'destroy'])->name('destroy');
     });
    });

    // ── Wali Santri ───────────────────────────────────────────────────────────
    Route::prefix('wali-santri')->name('wali-santri.')->group(function () {

        // Walikelas needs to view guardian contacts
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,WALIKELAS,STAFF_TU'])->group(function () {
            Route::get('/',        [WaliController::class, 'index'])->name('index');
            Route::get('/data',    [WaliController::class, 'getData'])->name('data');
            Route::get('/{id}',    [WaliController::class, 'show'])->name('show');
        });

        // CUD restricted to admin layer
        Route::middleware(['role:SUPERADMIN,ADMIN,KEPSEK,STAFF_TU'])->group(function () {
            Route::post('/',       [WaliController::class, 'store'])->name('store');
            Route::put('/{id}',    [WaliController::class, 'update'])->name('update');
            Route::delete('/{id}', [WaliController::class, 'destroy'])->name('destroy');
        });
    });

});