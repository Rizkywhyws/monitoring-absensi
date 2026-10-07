<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
{
    // TODO: ganti ke auth()->id() setelah fitur login selesai
    $userId = 1;

    $attendanceSetting = AttendanceSetting::where('status', 'active')->first();

    $todayAttendance = Attendance::where('user_id', $userId)
        ->where('attendance_date', now()->toDateString())
        ->first();

    return view('user.absensi', [
        'userName' => 'Refangga Ardiansah',
        'nim' => '240810101052',
        'attendanceSetting' => $attendanceSetting,
        'todayAttendance' => $todayAttendance,
    ]);
}
    public function checkIn(Request $request)
{
    // TODO: ganti ke auth()->id() setelah fitur login selesai
    $userId = 1;

    $request->validate([
        'photo' => ['required', 'string'],
    ]);

    $today = now()->toDateString();

    // Cegah check-in dobel di hari yang sama
    $existing = Attendance::where('user_id', $userId)
        ->where('attendance_date', $today)
        ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'Anda sudah melakukan check-in hari ini.',
        ], 422);
    }

    // --- Validasi lokasi GPS sementara di-skip (dummy) ---
    // TODO: aktifkan lagi validasi radius asli setelah masalah GPS selesai

    // Decode foto base64 (format: "data:image/jpeg;base64,XXXXX")
    $photoData = $request->photo;
    if (str_contains($photoData, ',')) {
        $photoData = explode(',', $photoData)[1];
    }
    $decodedPhoto = base64_decode($photoData);

    if ($decodedPhoto === false) {
        return response()->json([
            'success' => false,
            'message' => 'Foto tidak valid.',
        ], 422);
    }

    $fileName = 'checkin_' . $userId . '_' . now()->format('Ymd_His') . '.jpg';
    $path = 'attendance_photos/' . $fileName;

    Storage::disk('public')->put($path, $decodedPhoto);

    $now = now();
    $batasHadir = $now->copy()->setTime(8, 0, 0);
    $status = $now->lte($batasHadir) ? 'hadir' : 'telat';

    $attendance = Attendance::create([
        'user_id' => $userId,
        'attendance_date' => $today,
        'check_in' => $now,
        'check_in_photo' => $path,
        'status' => $status,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Check-in berhasil.',
        'status' => $status,
        'check_in' => $attendance->check_in->format('H:i:s'),
        'photo_url' => Storage::disk('public')->url($path),
    ]);
}
    private function calculateDistance(
    float $latitudeFrom,
    float $longitudeFrom,
    float $latitudeTo,
    float $longitudeTo
): float {
    $earthRadius = 6371000;

    $latFrom = deg2rad($latitudeFrom);
    $latTo = deg2rad($latitudeTo);

    $latDifference = deg2rad($latitudeTo - $latitudeFrom);
    $lonDifference = deg2rad($longitudeTo - $longitudeFrom);

    $a = sin($latDifference / 2) ** 2
        + cos($latFrom)
        * cos($latTo)
        * sin($lonDifference / 2) ** 2;

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}
}