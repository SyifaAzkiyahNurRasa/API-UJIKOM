<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | CRUD KATEGORI
        |--------------------------------------------------------------------------
        */

        Route::get('/kategori', [AdminController::class, 'indexKategori'])
            ->name('kategori.index');

        Route::get('/kategori/create', [AdminController::class, 'createKategori'])
            ->name('kategori.create');

        Route::post('/kategori', [AdminController::class, 'storeKategori'])
            ->name('kategori.store');

        Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])
            ->name('kategori.edit');

        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])
            ->name('kategori.update');

        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])
            ->name('kategori.destroy');


        /*
        |--------------------------------------------------------------------------
        | CRUD ALAT
        |--------------------------------------------------------------------------
        */

        Route::get('/alat', [AdminController::class, 'indexAlat'])
            ->name('alat.index');

        Route::get('/alat/create', [AdminController::class, 'createAlat'])
            ->name('alat.create');

        Route::post('/alat', [AdminController::class, 'storeAlat'])
            ->name('alat.store');

        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])
            ->name('alat.edit');

        Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])
            ->name('alat.update');

        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])
            ->name('alat.destroy');


        /*
        |--------------------------------------------------------------------------
        | CRUD USER
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [AdminController::class, 'indexUser'])
            ->name('user.index');

        Route::get('/users/create', [AdminController::class, 'createUser'])
            ->name('user.create');

        Route::post('/users', [AdminController::class, 'storeUser'])
            ->name('user.store');

        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])
            ->name('user.edit');

        Route::put('/users/{id}', [AdminController::class, 'updateUser'])
            ->name('user.update');

        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])
            ->name('user.destroy');


        /*
        |--------------------------------------------------------------------------
        | CRUD PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])
            ->name('peminjaman.create');

        Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])
            ->name('peminjaman.store');

        Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])
            ->name('peminjaman.updateStatus');

        Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])
            ->name('peminjaman.destroy');


        /*
        |--------------------------------------------------------------------------
        | KELOLA PENGEMBALIAN ADMIN
        |--------------------------------------------------------------------------
        */

        // Daftar pengembalian
        Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])
            ->name('pengembalian.index');

        // Form pengembalian
        Route::get('/pengembalian/{id}/create', [AdminController::class, 'createPengembalian'])
            ->name('pengembalian.create');

        // Proses pengembalian
        Route::post('/pengembalian/{id}/proses', [AdminController::class, 'kembalikan'])
            ->name('pengembalian.proses');

        // Update pengembalian
        Route::put('/pengembalian/{id}', [AdminController::class, 'kembalikan'])
            ->name('pengembalian.kembalikan');
    });


/*
|--------------------------------------------------------------------------
| PETUGAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:petugas,admin'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN & PERSETUJUAN
        |--------------------------------------------------------------------------
        */

        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])
            ->name('peminjaman.index');

        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])
            ->name('peminjaman.setujui');

        Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])
            ->name('peminjaman.tolak');


        /*
        |--------------------------------------------------------------------------
        | PENGEMBALIAN
        |--------------------------------------------------------------------------
        */

        // Daftar pengembalian
        Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])
            ->name('pengembalian.index');

        // Form proses pengembalian
        Route::get('/pengembalian/{id}/create', [PetugasController::class, 'createPengembalian'])
            ->name('pengembalian.create');

        // Proses pengembalian
        // Menggunakan POST agar sesuai dengan form pengembalian
        Route::post('/pengembalian/{id}/proses', [PetugasController::class, 'prosesPengembalian'])
            ->name('pengembalian.proses');


        /*
        |--------------------------------------------------------------------------
        | LAPORAN
        |--------------------------------------------------------------------------
        */

        Route::get('/laporan', [PetugasController::class, 'indexLaporan'])
            ->name('laporan.index');
    });


/*
|--------------------------------------------------------------------------
| PEMINJAM
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:peminjam'])
    ->prefix('peminjam')
    ->name('peminjam.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [PeminjamController::class, 'dashboard'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | KATALOG ALAT
        |--------------------------------------------------------------------------
        */

        Route::get('/alat', [PeminjamController::class, 'katalogAlat'])
            ->name('katalog');

        Route::get('/alat/{id}', [PeminjamController::class, 'detailAlat'])
            ->name('alat.detail');


        /*
        |--------------------------------------------------------------------------
        | PENGAJUAN PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        Route::get('/peminjaman', [PeminjamController::class, 'formPeminjaman'])
            ->name('peminjaman');

        Route::post('/peminjaman', [PeminjamController::class, 'ajukanPeminjaman'])
            ->name('peminjaman.ajukan');


        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        Route::get('/peminjaman-saya', [PeminjamController::class, 'riwayatPeminjaman'])
            ->name('riwayat');


        /*
        |--------------------------------------------------------------------------
        | PENGEMBALIAN
        |--------------------------------------------------------------------------
        */

        Route::get('/pengembalian', [PeminjamController::class, 'pengembalian'])
            ->name('pengembalian');

        Route::post('/pengembalian/{id}/ajukan', [PeminjamController::class, 'ajukanPengembalian'])
            ->name('pengembalian.ajukan');
    });


/*
|--------------------------------------------------------------------------
| ROUTE TAMU
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');