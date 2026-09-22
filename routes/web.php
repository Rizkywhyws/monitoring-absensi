<?php

use App\Http\Controllers\Auth\LoginController;
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

Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show')->whereNumber('ticket');
    Route::post('/tickets', [TicketController::class, 'store'])->name('ticket.store');
});

Route::get('/', function () {
    return view('welcome');
});
