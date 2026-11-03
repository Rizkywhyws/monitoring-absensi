<?php

use App\Http\Controllers\Admin\DaftarUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeaveApprovalController;
use App\Http\Controllers\Admin\RiwayatAbsensiController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\DailyActivityController as AdminDailyActivityController;
use App\Http\Controllers\User\DailyActivityController as UserDailyActivityController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TicketAdminController;

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

    Route::get('/izin', [LeaveApprovalController::class, 'index'])->name('izin');
    Route::put('/izin/{leave}/setujui', [LeaveApprovalController::class, 'approve'])->name('izin.setujui');
    Route::put('/izin/{leave}/tolak', [LeaveApprovalController::class, 'reject'])->name('izin.tolak');
    Route::put('/izin/{leave}/cabut', [LeaveApprovalController::class, 'revoke'])->name('izin.cabut');

    Route::get('/daily-activity', [AdminDailyActivityController::class, 'index'])->name('daily-activity.index');
    Route::get('/daily-activity/{user}', [AdminDailyActivityController::class, 'show'])->name('daily-activity.show');
    Route::post('/daily-activity/{user}', [AdminDailyActivityController::class, 'store'])->name('daily-activity.store');


    Route::get('/tickets', [TicketAdminController::class, 'index'])->name('tickets.index');
    Route::patch('/tickets/{ticket}/verify', [TicketAdminController::class, 'verify'])
        ->name('tickets.verify')->whereNumber('ticket');
    Route::patch('/tickets/{ticket}/revision', [TicketAdminController::class, 'revision'])
        ->name('tickets.revision')->whereNumber('ticket');
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
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $user = $request->user()->load('group');

        $mulai   = $user->periode_mulai?->translatedFormat('d M Y');
        $selesai = $user->periode_selesai?->translatedFormat('d M Y');
        $riwayatAbsensi = \App\Models\Attendance::where('user_id', $user->id)
            ->orderByDesc('attendance_date')
            ->limit(7)
            ->get();

        $totalTiket   = \Illuminate\Support\Facades\DB::table('tickets')
            ->where('user_id', $user->id)->count();
        $tiketSelesai = \Illuminate\Support\Facades\DB::table('tickets')
            ->where('user_id', $user->id)
            ->whereIn('status', ['done', 'verified'])->count();
        $totalHadir = \App\Models\Attendance::where('user_id', $user->id)
            ->whereNotNull('check_in')->count();
        $totalTelat = \App\Models\Attendance::where('user_id', $user->id)
            ->where('status', 'telat')->count();

        $todayAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        $lastCheckIn = \App\Models\Attendance::where('user_id', $user->id)
            ->whereNotNull('check_in')
            ->whereDate('attendance_date', '<', today())
            ->orderByDesc('attendance_date')
            ->first();

        $pendingLeave = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereDate('end_date', '>=', today())
            ->orderBy('start_date')
            ->first();

        $setting = \App\Models\AttendanceSetting::where('status', 'active')->first();

        $totalAlpha = 0;
        $totalSakit = 0;
        $totalIzin  = 0;

        if ($user->periode_mulai) {
            $awal  = $user->periode_mulai->copy()->startOfDay();
            $akhir = today()->subDay();

            if ($user->periode_selesai && $user->periode_selesai->lt($akhir)) {
                $akhir = $user->periode_selesai->copy()->startOfDay();
            }

            if ($awal->lte($akhir)) {
                $hariKerja = 0;
                for ($d = $awal->copy(); $d->lte($akhir); $d->addDay()) {
                    if ($d->isWeekday()) {
                        $hariKerja++;
                    }
                }

                $catatan = \App\Models\Attendance::where('user_id', $user->id)
                    ->whereBetween('attendance_date', [$awal->toDateString(), $akhir->toDateString()])
                    ->get(['attendance_date', 'status']);

                $totalSakit = $catatan->where('status', 'sakit')->count();
                $totalIzin  = $catatan->where('status', 'izin')->count();
                $tercatat   = $catatan->filter(fn($a) => $a->attendance_date->isWeekday())->count();
                $totalAlpha = max($hariKerja - $tercatat, 0);
            }
        }

        $totalTidakHadir = $totalAlpha + $totalSakit + $totalIzin;

        return view('user.dashboard', [
            'userName'        => $user->name,
            'nim'             => $user->nim,
            'periodeMagang'   => ($mulai && $selesai) ? "$mulai – $selesai" : '-',
            'divisi'          => $user->group?->name ?? '-',
            'pembimbing'      => $user->mentor ?? '-',
            'kampus'          => $user->asal_universitas ?? '-',
            'riwayatAbsensi'  => $riwayatAbsensi,
            'totalTiket'      => $totalTiket,
            'tiketSelesai'    => $tiketSelesai,
            'totalHadir'      => $totalHadir,
            'totalTelat'      => $totalTelat,
            'totalAlpha'      => $totalAlpha,
            'totalSakit'      => $totalSakit,
            'totalIzin'       => $totalIzin,
            'totalTidakHadir' => $totalTidakHadir,
            'todayAttendance' => $todayAttendance,
            'lastCheckIn'     => $lastCheckIn,
            'pendingLeave'    => $pendingLeave,
            'lateAfter'       => '08:15',
            'office'          => $setting ? [
                'name'   => $setting->name,
                'radius' => (float) $setting->radius,
            ] : null,
        ]);
    })->name('dashboard');

    Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi');
    Route::get('/absensi/riwayat', [AttendanceController::class, 'riwayat'])->name('absensi.riwayat');
    Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])
        ->middleware('throttle:10,1')->name('absensi.check-in');
    Route::post('/absensi/check-out', [AttendanceController::class, 'checkOut'])
        ->middleware('throttle:10,1')->name('absensi.check-out');
    Route::get('/absensi/foto/{attendance}/{type}', [AttendanceController::class, 'photo'])
        ->name('absensi.photo');

    Route::get('/izin', [LeaveRequestController::class, 'index'])->name('izin');
    Route::post('/izin', [LeaveRequestController::class, 'store'])
        ->middleware('throttle:10,1')->name('izin.store');
    Route::put('/izin/{leave}/batal', [LeaveRequestController::class, 'cancel'])->name('izin.batal');
    Route::get('/izin/{leave}/lampiran', [LeaveRequestController::class, 'attachment'])->name('izin.lampiran');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show')->whereNumber('ticket');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update')->whereNumber('ticket');
    Route::patch('/tickets/{ticket}/start', [TicketController::class, 'start'])->name('ticket.start')->whereNumber('ticket');
    Route::patch('/tickets/{ticket}/complete', [TicketController::class, 'complete'])->name('ticket.complete')->whereNumber('ticket');

    Route::get('/daily-activity', [UserDailyActivityController::class, 'index'])->name('daily-activity.index');
    Route::post('/daily-activity', [UserDailyActivityController::class, 'store'])->name('daily-activity.store');

    Route::get('/profil', [\App\Http\Controllers\User\ProfileController::class, 'show'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profil.update');

    Route::get('/user', fn() => view('user.index'))->name('user.index');
});
