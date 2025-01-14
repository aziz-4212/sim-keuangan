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

    // =====================================Klaim Jasa Medis Non Tindakan===========================================
    Route::get('/cek-klaim-non-tindakan', [KlaimController::class, 'cek_klaim_non_tindakan'])->name('cek-klaim');
    Route::post('/cek-klaim-non-tindakan/proses-selisih', [KlaimController::class, 'proses_selisih_non_tindakan'])->name('proses-selisih');
    Route::get('/cek-klaim-non-tindakan/selisih-minus', [KlaimController::class, 'cek_klaim_minus_selisih_non_tindakan'])->name('cek-klaim-minus-selisih');
    Route::get('/cek-klaim-non-tindakan/jasa-visit-minus', [KlaimController::class, 'jasa_visit_minus_non_tindakan'])->name('jasa-visit-minus');
    Route::get('/detail-jasa-visit', [KlaimController::class, 'get_detail_jasa_visit']);
    Route::post('/cek-klaim-non-tindakan/jasa-visit-minus/update-jasa-visit', [KlaimController::class, 'update_jasa_visit'])->name('update-jasa-visit');
    // =====================================End Klaim Jasa Medis Non Tindakan===========================================

    // =====================================Klaim Jasa Medis Tindakan IBS===========================================
    Route::get('/cek-klaim-tindakan/selisih-minus', [KlaimController::class, 'cek_klaim_minus_selisih_tindakan'])->name('cek-klaim-minus-selisih-tindakan');
    Route::get('/cek-klaim-tindakan/jasa-medis-minus', [KlaimController::class, 'jasa_medis_minus_tindakan'])->name('jasa-medis-minus-tindakan');
    Route::get('/detail-jasa-medis-tindakan', [KlaimController::class, 'get_detail_jasa_medis_tindakan']);
    Route::post('/cek-klaim-tindakan/jasa-medis-minus/update-jasa-medis', [KlaimController::class, 'update_jasa_medis_tindakan'])->name('update-jasa-medis-tindakan');
    // =====================================End Klaim Jasa Medis Tindakan IBS===========================================

});
