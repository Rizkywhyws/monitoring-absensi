<?php

use App\Http\Controllers\Admin\DaftarUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RiwayatAbsensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyActivityController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(
            Auth::user()->role === 'admin' ? 'admin.dashboard' : 'dashboard'
        );
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/riwayat-absensi', [RiwayatAbsensiController::class, 'index'])->name('riwayat-absensi');



    Route::get('/daily-activity', [DailyActivityController::class, 'index'])->name('daily-activity.index');
    Route::get('/daily-activity/{user}', [DailyActivityController::class, 'show'])->name('daily-activity.show');
    Route::post('/daily-activity/{user}', [DailyActivityController::class, 'store'])->name('daily-activity.store');

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

Route::get('/absensi', [AttendanceController::class, 'index'])
    ->name('absensi');
Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])
    ->name('absensi.check-in');

Route::get('/absensi', function () {
    return view('user.absensi', [
        'userName' => 'Refangga Ardiansah',
        'nim'      => '240810101052',
    ]);
})->name('absensi');


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
    Route::get('/dashboard', function (Request $request) {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

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