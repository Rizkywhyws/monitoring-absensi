<?php
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

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

Route::get('/', function () {
    return view('welcome');
});
