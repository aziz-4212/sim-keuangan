<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/loginCheck', [AuthController::class, 'loginCheck'])->name('loginCheck');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/download', [LaporanController::class, 'download_excel'])->name('laporan.download-excel');

    Route::get('/jasamedis', [JasaMedisController::class, 'jasaMedis'])->name('jasaMedis');

    // =====================================Klaim Jasa Medis===========================================
    Route::get('/cek-klaim', [KlaimController::class, 'cek_klaim'])->name('cek-klaim');
    Route::post('/cek-klaim/proses-selisih', [KlaimController::class, 'proses_selisih'])->name('proses-selisih');
    Route::get('/cek-klaim/selisih-minus', [KlaimController::class, 'cek_klaim_minus_selisih'])->name('cek-klaim-minus-selisih');
    Route::get('/cek-klaim/jasa-visit-minus', [KlaimController::class, 'jasa_visit_minus'])->name('jasa-visit-minus');
    Route::get('/detail-jasa-visit', [KlaimController::class, 'get_detail_jasa_visit']);
    Route::post('/cek-klaim/jasa-visit-minus/update-jasa-visit', [KlaimController::class, 'update_jasa_visit'])->name('update-jasa-visit');
    // =====================================End Klaim Jasa Medis===========================================

});
