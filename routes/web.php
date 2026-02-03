<?php

use App\Http\Controllers\StatsController;
use App\Http\Controllers\SantriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stats/santri', [StatsController::class, 'santri'])->name('stats.santri');

Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');


