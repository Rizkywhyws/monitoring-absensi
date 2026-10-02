<?php

use App\Http\Controllers\Admin\DaftarUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', fn() => view('welcome'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn() => view('admin.dashboardAdmin'))->name('dashboard');

    Route::controller(DaftarUserController::class)->prefix('daftar-user')->name('daftar-user')->group(function () {
        Route::get('/', 'index');
        Route::get('/tambah', 'tambah')->name('.tambah');
        Route::post('/simpan', 'simpan')->name('.simpan');
        Route::get('/{id}', 'detail')->name('.detail');
        Route::get('/{id}/edit', 'edit')->name('.edit');
        Route::put('/{id}', 'update')->name('.update');
        Route::put('/{id}/kelompok', 'ubahKelompok')->name('.kelompok');
        Route::put('/{id}/reset-password', 'resetPassword')->name('.reset-password');
        Route::put('/{id}/aktifkan', 'aktifkan')->name('.aktifkan');
        Route::put('/{id}/nonaktifkan', 'nonaktifkan')->name('.nonaktifkan');
        Route::delete('/{id}', 'hapus')->name('.hapus');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user()->load('group');

        $mulai   = $user->periode_mulai?->translatedFormat('d M Y');
        $selesai = $user->periode_selesai?->translatedFormat('d M Y');

        return view('user.dashboard', [
            'userName'      => $user->name,
            'nim'           => $user->nim,
            'periodeMagang' => ($mulai && $selesai) ? "$mulai – $selesai" : '-',
            'divisi'        => $user->group?->name ?? '-',
            'pembimbing'    => $user->mentor ?? '-',
            'kampus'        => $user->asal_universitas ?? '-',
        ]);
    })->name('dashboard');

    Route::get('/absensi', fn(Request $request) => view('user.absensi', [
        'userName' => $request->user()->name,
        'nim'      => $request->user()->nim,
    ]))->name('absensi');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show')->whereNumber('ticket');
    Route::post('/tickets', [TicketController::class, 'store'])->name('ticket.store');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
});
