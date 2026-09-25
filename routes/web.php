<?php

use App\Http\Controllers\Admin\DaftarUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\TambahUserController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

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


Route::get('/absensi', function () {
    return view('user.absensi', [
        'userName' => 'Refangga Ardiansah',
        'nim'      => '240810101052',
    ]);
})->name('absensi');


Route::get('/dashboard/admin', [TambahUserController::class, 'create'])
    ->name('tambahuser');

Route::post('/dashboard/admin', [TambahUserController::class, 'store'])
    ->name('tambahuser.store');


Route::get('/user', function () {
    return view('user.index');
})->name('user.index');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/daftar-user', [
            DaftarUserController::class,
            'index'
        ])->name('daftar-user');

        Route::get('/daftar-user/tambah', [
            DaftarUserController::class,
            'tambah'
        ])->name('daftar-user.tambah');

        Route::post('/daftar-user/simpan', [
            DaftarUserController::class,
            'simpan'
        ])->name('daftar-user.simpan');

        Route::get('/daftar-user/{id}/edit', [
            DaftarUserController::class,
            'edit'
        ])->name('daftar-user.edit');

        Route::put('/daftar-user/{id}', [
            DaftarUserController::class,
            'update'
        ])->name('daftar-user.update');

        Route::put('/daftar-user/{id}/kelompok', [
            DaftarUserController::class,
            'ubahKelompok'
        ])->name('daftar-user.kelompok');

        Route::put('/daftar-user/{id}/reset-password', [
            DaftarUserController::class,
            'resetPassword'
        ])->name('daftar-user.reset-password');

        Route::put('/daftar-user/{id}/aktifkan', [
            DaftarUserController::class,
            'aktifkan'
        ])->name('daftar-user.aktifkan');

        Route::put('/daftar-user/{id}/nonaktifkan', [
            DaftarUserController::class,
            'nonaktifkan'
        ])->name('daftar-user.nonaktifkan');


        Route::delete('/daftar-user/{id}', [
            DaftarUserController::class,
            'hapus'
        ])->name('daftar-user.hapus');


        Route::get('/daftar-user/{id}', [
            DaftarUserController::class,
            'detail'
        ])->name('daftar-user.detail');

    });
Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show')->whereNumber('ticket');
    Route::post('/tickets', [TicketController::class, 'store'])->name('ticket.store');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
});

Route::get('/', function () {
    return view('welcome');
});
