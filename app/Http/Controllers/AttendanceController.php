<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    private const LATE_AFTER = '08:15:00';
    private const MAX_ACCURACY_METERS = 100;
    private const MAX_PHOTO_BYTES = 2097152;

    public function index(Request $request)
    {
        $user = $request->user();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderByDesc('attendance_date')
            ->limit(5)
            ->get();

        $mentorInitials = Str::of((string) $user->mentor)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $setting = AttendanceSetting::where('status', 'active')->first();

        return view('user.absensi', [
            'attendanceSetting' => $setting,
            'office' => $setting ? [
                'name'   => $setting->name,
                'lat'    => (float) $setting->latitude,
                'lng'    => (float) $setting->longitude,
                'radius' => (float) $setting->radius,
            ] : null,
            'todayAttendance' => $todayAttendance,
            'recentAttendances' => $recentAttendances,
            'pembimbing' => $user->mentor,
            'pembimbingInitials' => $mentorInitials ?: '-',
            'lateAfter' => now()->setTimeFromTimeString(self::LATE_AFTER),
            'maxAccuracy' => self::MAX_ACCURACY_METERS,
        ]);
    }

    public function riwayat(Request $request)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $month = (int) $request->query('bulan', now()->month);
        if (! isset($months[$month])) {
            $month = now()->month;
        }

        $year = (int) $request->query('tahun', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $status = $request->query('status');
        if (! in_array($status, ['hadir', 'telat'], true)) {
            $status = null;
        }

        $userId = $request->user()->id;
        $start  = Carbon::create($year, $month, 1)->startOfMonth();
        $end    = $start->copy()->endOfMonth();

        $monthQuery = Attendance::where('user_id', $userId)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);

        $monthRows = (clone $monthQuery)->get(['status', 'check_in', 'check_out']);

        $durations = $monthRows
            ->filter(fn($r) => $r->check_in && $r->check_out)
            ->map(fn($r) => intdiv($r->check_out->getTimestamp() - $r->check_in->getTimestamp(), 60));

        $summary = [
            'total'       => $monthRows->count(),
            'hadir'       => $monthRows->where('status', 'hadir')->count(),
            'telat'       => $monthRows->where('status', 'telat')->count(),
            'avg_minutes' => $durations->isNotEmpty() ? (int) round($durations->avg()) : null,
        ];

        $attendances = (clone $monthQuery)
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('attendance_date')
            ->paginate(10)
            ->withQueryString();

        $earliest  = Attendance::where('user_id', $userId)->min('attendance_date');
        $firstYear = $earliest ? Carbon::parse($earliest)->year : now()->year;
        $years     = range(now()->year, min($firstYear, now()->year));

        return view('user.riwayatAbsensi', compact(
            'attendances',
            'months',
            'month',
            'year',
            'years',
            'status',
            'summary'
        ));
    }

    public function checkIn(Request $request): JsonResponse
    {
        $data = $this->validatedPayload($request);
        $user = $request->user();
        $today = today()->toDateString();

        $alreadyCheckedIn = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            return $this->fail('Anda sudah melakukan check-in hari ini.');
        }

        [$distance, $locationError] = $this->validateLocation($data);
        if ($locationError) {
            return $this->fail($locationError);
        }

        $path = $this->storePhoto($data['photo'], $user->id, 'in');
        if (! $path) {
            return $this->fail('Foto tidak valid atau lebih dari 2 MB.');
        }

        $now = now();
        $status = $now->lte(now()->setTimeFromTimeString(self::LATE_AFTER)) ? 'hadir' : 'telat';

        try {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'attendance_date' => $today,
                'check_in' => $now,
                'check_in_latitude' => $data['latitude'],
                'check_in_longitude' => $data['longitude'],
                'check_in_accuracy' => $data['accuracy'],
                'check_in_distance' => $distance,
                'check_in_photo' => $path,
                'status' => $status,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            Storage::disk('local')->delete($path);

            return $this->fail('Anda sudah melakukan check-in hari ini.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil.',
            'status' => $status,
            'check_in' => $attendance->check_in->format('H:i:s'),
            'distance' => round($distance),
        ]);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $data = $this->validatedPayload($request);
        $user = $request->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (! $attendance) {
            return $this->fail('Anda belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out) {
            return $this->fail('Anda sudah melakukan check-out hari ini.');
        }

        [$distance, $locationError] = $this->validateLocation($data);
        if ($locationError) {
            return $this->fail($locationError);
        }

        $path = $this->storePhoto($data['photo'], $user->id, 'out');
        if (! $path) {
            return $this->fail('Foto tidak valid atau lebih dari 2 MB.');
        }

        $updated = Attendance::where('id', $attendance->id)
            ->whereNull('check_out')
            ->update([
                'check_out' => now(),
                'check_out_latitude' => $data['latitude'],
                'check_out_longitude' => $data['longitude'],
                'check_out_accuracy' => $data['accuracy'],
                'check_out_distance' => $distance,
                'check_out_photo' => $path,
                'updated_at' => now(),
            ]);

        if (! $updated) {
            Storage::disk('local')->delete($path);

            return $this->fail('Anda sudah melakukan check-out hari ini.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil.',
            'check_out' => $attendance->fresh()->check_out->format('H:i:s'),
            'distance' => round($distance),
        ]);
    }

    public function photo(Request $request, Attendance $attendance, string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['in', 'out'], true), 404);

        $user = $request->user();
        abort_unless($user->role === 'admin' || $attendance->user_id === $user->id, 403);

        $path = $type === 'in' ? $attendance->check_in_photo : $attendance->check_out_photo;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'photo' => ['required', 'string', 'max:3000000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function validateLocation(array $data): array
    {
        $setting = AttendanceSetting::where('status', 'active')->first();

        if (! $setting) {
            return [null, 'Lokasi kantor belum dikonfigurasi. Hubungi admin.'];
        }

        if ((float) $data['accuracy'] > self::MAX_ACCURACY_METERS) {
            return [null, 'Akurasi GPS terlalu rendah (±' . round($data['accuracy']) . ' m, maksimal ' . self::MAX_ACCURACY_METERS . ' m). Coba di area terbuka atau gunakan ponsel.'];
        }

        $distance = $this->calculateDistance(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $setting->latitude,
            (float) $setting->longitude
        );

        if ($distance > (float) $setting->radius) {
            return [$distance, 'Anda berada ' . round($distance) . ' m dari kantor. Batas radius ' . round($setting->radius) . ' m, sehingga absensi ditolak.'];
        }

        return [$distance, null];
    }

    private function storePhoto(string $base64, int $userId, string $type): ?string
    {
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64, 2)[1];
        }

        $binary = base64_decode($base64, true);

        if ($binary === false || $binary === '' || strlen($binary) > self::MAX_PHOTO_BYTES) {
            return null;
        }

        $info = @getimagesizefromstring($binary);

        if ($info === false || $info['mime'] !== 'image/jpeg') {
            return null;
        }

        $path = 'attendance_photos/' . $userId . '/' . $type . '_' . Str::uuid() . '.jpg';
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    private function fail(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }

    private function calculateDistance(float $latFrom, float $lonFrom, float $latTo, float $lonTo): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($latTo - $latFrom);
        $dLon = deg2rad($lonTo - $lonFrom);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
