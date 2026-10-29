<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user kamu; kalau tidak ketemu, pakai user pertama
        $user = User::findOrFail(31);

        // 10 hari kerja terakhir: [status, jam check-in, jam check-out, jarak (meter)]
        $pola = [
            ['hadir', '07:52', '17:05', 18],
            ['hadir', '07:58', '17:10', 42],
            ['telat', '08:32', '17:02', 25],
            ['hadir', '07:49', '17:15', 12],
            ['hadir', '07:55', '17:00', 37],
            ['hadir', '07:51', '17:08', 30],
            ['telat', '08:21', '17:12', 55],
            ['hadir', '08:03', '17:08', 22],
            ['hadir', '07:57', '17:04', 48],
            ['hadir', '07:54', '17:06', 15],
        ];

        $tanggal = Carbon::yesterday();

        foreach ($pola as [$status, $in, $out, $jarak]) {
            // Lewati Sabtu & Minggu
            while ($tanggal->isWeekend()) {
                $tanggal->subDay();
            }

            Attendance::updateOrCreate(
                ['user_id' => $user->id, 'attendance_date' => $tanggal->toDateString()],
                [
                    'check_in'           => $tanggal->copy()->setTimeFromTimeString($in),
                    'check_out'          => $tanggal->copy()->setTimeFromTimeString($out),
                    'check_in_distance'  => $jarak,
                    'check_out_distance' => $jarak,
                    'status'             => $status,
                ]
            );

            $tanggal->subDay();
        }
    }
}