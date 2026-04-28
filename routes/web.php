<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\ProgresS2Controller;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RefleksiController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TugasController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', fn() => view('auth.login'))->name('login')->middleware('guest');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/note', [DashboardController::class, 'saveNote'])->name('note.save');

Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

Route::post('/progres', [ProgresS2Controller::class, 'store'])->name('progres.store');
Route::patch('/progres/{progres}', [ProgresS2Controller::class, 'update'])->name('progres.update');
Route::delete('/progres/{progres}', [ProgresS2Controller::class, 'destroy'])->name('progres.destroy');

Route::post('/semester', [SemesterController::class, 'store'])->name('semester.store');
Route::patch('/semester/{semester}/activate', [SemesterController::class, 'activate'])->name('semester.activate');
Route::delete('/semester/{semester}', [SemesterController::class, 'destroy'])->name('semester.destroy');

Route::post('/profil/whatsapp', [ProfilController::class, 'update'])->name('profil.whatsapp');

Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
Route::delete('/jurnal/{jurnal}', [JurnalController::class, 'destroy'])->name('jurnal.destroy');

Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
Route::patch('/tugas/{tugas}/status', [TugasController::class, 'updateStatus'])->name('tugas.status');
Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy');

Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
Route::get('/materi/{materi}', [MateriController::class, 'show'])->name('materi.show');
Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');

Route::get('/refleksi', [RefleksiController::class, 'index'])->name('refleksi.index');
Route::post('/refleksi', [RefleksiController::class, 'store'])->name('refleksi.store');
Route::delete('/refleksi/{refleksi}', [RefleksiController::class, 'destroy'])->name('refleksi.destroy');

}); // end auth middleware
