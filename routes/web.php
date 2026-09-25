<?php

use App\Http\Controllers\Admin\DaftarUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\TambahUserController;
use Illuminate\Support\Facades\Route;

// =========================
// AUTH
// =========================
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


// =========================
// HALAMAN UTAMA
// =========================
Route::get('/', function () {
    return view('welcome');
});


// =========================
// DASHBOARD USER
// =========================
Route::get('/dashboard', function () {
    return view('user.dashboard', [
        'userName'      => 'Refangga Ardiansah',
        'initials'      => 'RA',
        'nim'           => '240810101052',
        'periodeMagang' => '01 Agu 2026 – 30 Sep 2026',
        'divisi'        => 'Instalasi & Jaringan',
        'pembimbing'    => 'Bpk. Hendra Kusuma',
        'kampus'        => 'Politeknik Negeri Jember',
    ]);
})->name('dashboard');


// =========================
// ABSENSI
// =========================
Route::get('/absensi', function () {
    return view('user.absensi', [
        'userName' => 'Refangga Ardiansah',
        'nim'      => '240810101052',
    ]);
})->name('absensi');


// =========================
// TAMBAH USER LAMA
// =========================
Route::get('/dashboard/admin', [TambahUserController::class, 'create'])
    ->name('tambahuser');

Route::post('/dashboard/admin', [TambahUserController::class, 'store'])
    ->name('tambahuser.store');


// =========================
// USER
// =========================
Route::get('/user', function () {
    return view('user.index');
})->name('user.index');


// =====================================================
// ADMIN
// =====================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // =========================
        // DAFTAR USER
        // GET /admin/daftar-user
        // =========================
        Route::get('/daftar-user', [
            DaftarUserController::class,
            'index'
        ])->name('daftar-user');


        // =========================
        // TAMBAH USER
        // GET /admin/daftar-user/tambah
        // =========================
        Route::get('/daftar-user/tambah', [
            DaftarUserController::class,
            'tambah'
        ])->name('daftar-user.tambah');


        // =========================
        // SIMPAN USER
        // POST /admin/daftar-user/simpan
        // =========================
        Route::post('/daftar-user/simpan', [
            DaftarUserController::class,
            'simpan'
        ])->name('daftar-user.simpan');


        // =========================
        // EDIT USER
        // GET /admin/daftar-user/{id}/edit
        // =========================
        Route::get('/daftar-user/{id}/edit', [
            DaftarUserController::class,
            'edit'
        ])->name('daftar-user.edit');


        // =========================
        // UPDATE USER
        // PUT /admin/daftar-user/{id}
        // =========================
        Route::put('/daftar-user/{id}', [
            DaftarUserController::class,
            'update'
        ])->name('daftar-user.update');


        // =========================
        // UBAH KELOMPOK
        // PUT /admin/daftar-user/{id}/kelompok
        // =========================
        Route::put('/daftar-user/{id}/kelompok', [
            DaftarUserController::class,
            'ubahKelompok'
        ])->name('daftar-user.kelompok');


        // =========================
        // RESET PASSWORD
        // PUT /admin/daftar-user/{id}/reset-password
        // =========================
        Route::put('/daftar-user/{id}/reset-password', [
            DaftarUserController::class,
            'resetPassword'
        ])->name('daftar-user.reset-password');


        // =========================
        // AKTIFKAN USER
        // PUT /admin/daftar-user/{id}/aktifkan
        // =========================
        Route::put('/daftar-user/{id}/aktifkan', [
            DaftarUserController::class,
            'aktifkan'
        ])->name('daftar-user.aktifkan');


        // =========================
        // NONAKTIFKAN USER
        // PUT /admin/daftar-user/{id}/nonaktifkan
        // =========================
        Route::put('/daftar-user/{id}/nonaktifkan', [
            DaftarUserController::class,
            'nonaktifkan'
        ])->name('daftar-user.nonaktifkan');


        // =========================
        // HAPUS USER
        // DELETE /admin/daftar-user/{id}
        // =========================
        Route::delete('/daftar-user/{id}', [
            DaftarUserController::class,
            'hapus'
        ])->name('daftar-user.hapus');


        // =========================
        // DETAIL USER
        // HARUS PALING BAWAH
        // GET /admin/daftar-user/{id}
        // =========================
        Route::get('/daftar-user/{id}', [
            DaftarUserController::class,
            'detail'
        ])->name('daftar-user.detail');

    });