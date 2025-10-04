<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratJalanController;
use Illuminate\Support\Facades\Route;

// hanya admin bisa buat surat jalan
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('/surat/create', [SuratJalanController::class,'create'])->name('surat.create');
    Route::post('/surat', [SuratJalanController::class,'store'])->name('surat.store');
});

// semua user login bisa lihat daftar & detail
Route::middleware(['auth'])->group(function(){
    Route::get('/', [SuratJalanController::class,'index'])->name('surat.index');
    Route::get('/surat/{id}', [SuratJalanController::class,'show'])->name('surat.show');
});

// kurir khusus bisa update lokasi & upload bukti
Route::middleware(['auth','role:kurir'])->group(function(){
    Route::post('/api/surat/update-location', [SuratJalanController::class,'updateLocation'])->name('surat.update.location');
    Route::post('/surat/{id}/upload-proof', [SuratJalanController::class,'uploadProof'])->name('surat.upload.proof');
});

// scan QR public, tapi update tetap butuh login kurir
Route::get('/scan/code/{code}', [SuratJalanController::class,'scanByCode'])->name('surat.scan.code');

// lokasi terbaru untuk Maps 
Route::get('/surat/{id}/locations/latest', [SuratJalanController::class,'latestLocation'])->name('surat.latest.location');

Route::delete('/surat/{id}', [SuratJalanController::class,'destroy'])->name('surat.destroy');

require __DIR__.'/auth.php';
