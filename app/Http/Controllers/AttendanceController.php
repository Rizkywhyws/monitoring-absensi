<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    private const LATE_AFTER = '08:00:00';
    private const MAX_ACCURACY_METERS = 100;
    private const MAX_PHOTO_BYTES = 2097152;
    private const PHOTO_MAX_WIDTH = 800;
    private const PHOTO_QUALITY = 70;
    private const EARTH_RADIUS_METERS = 6371000;
    private const STATUS_ON_TIME = 'hadir';
    private const STATUS_LATE    = 'telat';
    private const MSG_ALREADY_CHECKED_IN = 'Anda sudah melakukan absensi hari ini.';
    private const MONTHS = [
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

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.absensi', [
            'offices'            => $this->officesForMap(),
            'todayAttendance'    => Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first(),
            'recentAttendances'  => Attendance::where('user_id', $user->id)
                ->orderByDesc('attendance_date')
                ->limit(5)
                ->get(),
            'pendingLeave'       => LeaveRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereDate('end_date', '>=', today())
                ->orderBy('start_date')
                ->first(),
            'pembimbing'         => $user->mentor,
            'pembimbingInitials' => $this->initials((string) $user->mentor),
            'lateAfter'          => $this->lateDeadline(now()),
            'maxAccuracy'        => self::MAX_ACCURACY_METERS,
        ]);
    }

    public function riwayat(Request $request): View
    {
        $user = $request->user();

        $filters = $request->validate([
            'bulan'  => ['nullable', 'integer', 'between:1,12'],
            'tahun'  => ['nullable', 'integer', 'between:2020,2100'],
            'status' => ['nullable', 'in:hadir,telat,sakit,izin,alpha'],
        ]);

        $month  = (int) ($filters['bulan'] ?? now()->month);
        $year   = (int) ($filters['tahun'] ?? now()->year);
        $status = $filters['status'] ?? null;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $monthQuery = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);

        $monthRecords = (clone $monthQuery)->get(['id', 'attendance_date', 'status']);
        $counts       = $monthRecords->countBy('status');
        $firstDate    = Attendance::where('user_id', $user->id)->min('attendance_date');
        $alphaDates   = $this->missedWorkdays($user, $firstDate, $start, $end, $monthRecords);

        $hadir = $counts->get(self::STATUS_ON_TIME, 0);
        $telat = $counts->get(self::STATUS_LATE, 0);

        $summary = [
            'total'       => $hadir + $telat,
            'hadir'       => $hadir,
            'telat'       => $telat,
            'sakit'       => $counts->get('sakit', 0),
            'izin'        => $counts->get('izin', 0),
            'alpha'       => count($alphaDates),
            'avg_minutes' => null,
        ];

        $attendances = (clone $monthQuery)
            ->when($status && $status !== 'alpha', fn($q) => $q->where('status', $status))
            ->orderByDesc('attendance_date')
            ->paginate(10)
            ->withQueryString();

        $firstYear = $firstDate ? Carbon::parse($firstDate)->year : now()->year;

        return view('user.riwayatAbsensi', [
            'attendances' => $attendances,
            'summary'     => $summary,
            'alphaDates'  => $alphaDates,
            'month'       => $month,
            'year'        => $year,
            'status'      => $status,
            'years'       => range(now()->year, min($firstYear, now()->year)),
            'months'      => self::MONTHS,
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $data = $this->validatedPayload($request);
        $user = $request->user();

        if ($this->hasAttendance($user, today())) {
            return $this->fail(self::MSG_ALREADY_CHECKED_IN);
        }

        ['office' => $office, 'distance' => $distance] = $this->verifyLocation($data);
        $photoPath = $this->storePhoto($data['photo'], $user->id);

        $now    = now();
        $status = $now->lte($this->lateDeadline($now)) ? self::STATUS_ON_TIME : self::STATUS_LATE;

        try {
            $attendance = Attendance::create([
                'user_id'               => $user->id,
                'attendance_setting_id' => $office->id,
                'attendance_date'       => today()->toDateString(),
                'check_in'              => $now,
                'check_in_latitude'     => $data['latitude'],
                'check_in_longitude'    => $data['longitude'],
                'check_in_accuracy'     => $data['accuracy'],
                'check_in_distance'     => $distance,
                'check_in_photo'        => $photoPath,
                'status'                => $status,
            ]);
        } catch (UniqueConstraintViolationException) {
            Storage::disk('local')->delete($photoPath);

            return $this->fail(self::MSG_ALREADY_CHECKED_IN);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Absensi berhasil.',
            'status'   => $status,
            'check_in' => $attendance->check_in->format('H:i:s'),
            'distance' => round($distance),
            'office'   => $office->name,
        ]);
    }

    public function photo(Request $request, Attendance $attendance, string $type): StreamedResponse
    {
        abort_unless($type === 'in', 404);

        $user = $request->user();
        abort_unless($user->role === 'admin' || $attendance->user_id === $user->id, 403);

        $path = $attendance->check_in_photo;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'photo'     => ['required', 'string', 'max:3000000'],
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['required', 'numeric', 'min:0'],
        ]);
    }

    /**
     * Pastikan koordinat berada di radius salah satu kantor aktif.
     * Koordinat kantor selalu dibaca dari tabel attendance_settings.
     *
     * @return array{office: AttendanceSetting, distance: float}
     */
    private function verifyLocation(array $data): array
    {
        $offices = AttendanceSetting::where('status', 'active')->get();

        if ($offices->isEmpty()) {
            $this->reject('Lokasi kantor belum dikonfigurasi. Hubungi admin.');
        }

        if ((float) $data['accuracy'] > self::MAX_ACCURACY_METERS) {
            $this->reject(sprintf(
                'Akurasi GPS terlalu rendah (±%d m, maksimal %d m). Coba di area terbuka atau gunakan ponsel.',
                (int) round($data['accuracy']),
                self::MAX_ACCURACY_METERS
            ));
        }

        $measured = $offices->map(fn(AttendanceSetting $office) => [
            'office'   => $office,
            'distance' => $this->distanceInMeters(
                (float) $data['latitude'],
                (float) $data['longitude'],
                (float) $office->latitude,
                (float) $office->longitude
            ),
        ]);

        $match = $measured
            ->filter(fn(array $m) => $m['distance'] <= (float) $m['office']->radius)
            ->sortBy('distance')
            ->first();

        if (! $match) {
            $nearest = $measured->sortBy('distance')->first();

            $this->reject(sprintf(
                'Anda berada %d m dari %s. Batas radius %d m, sehingga absensi ditolak.',
                (int) round($nearest['distance']),
                $nearest['office']->name,
                (int) round((float) $nearest['office']->radius)
            ));
        }

        return $match;
    }
    private function storePhoto(string $base64, int $userId): string
    {
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64, 2)[1];
        }

        $binary = base64_decode($base64, true);

        if ($binary === false || $binary === '' || strlen($binary) > self::MAX_PHOTO_BYTES) {
            $this->rejectInvalidPhoto();
        }

        $info = @getimagesizefromstring($binary);

        if ($info === false || $info['mime'] !== 'image/jpeg') {
            $this->rejectInvalidPhoto();
        }

        $binary = $this->compressJpeg($binary, (int) $info[0], (int) $info[1]);

        $path = sprintf('attendance_photos/%d/%s.jpg', $userId, Str::uuid());
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    private function compressJpeg(string $binary, int $width, int $height): string
    {
        if (! function_exists('imagecreatefromstring')) {
            return $binary;
        }

        $image = @imagecreatefromstring($binary);

        if (! $image) {
            return $binary;
        }

        if ($width > self::PHOTO_MAX_WIDTH) {
            $resized = imagescale(
                $image,
                self::PHOTO_MAX_WIDTH,
                (int) round($height * self::PHOTO_MAX_WIDTH / $width)
            );

            if ($resized) {
                imagedestroy($image);
                $image = $resized;
            }
        }

        ob_start();
        imagejpeg($image, null, self::PHOTO_QUALITY);
        $compressed = ob_get_clean();
        imagedestroy($image);

        return ($compressed !== false && $compressed !== '' && strlen($compressed) < strlen($binary))
            ? $compressed
            : $binary;
    }

    /** Haversine. */
    private function distanceInMeters(float $latFrom, float $lonFrom, float $latTo, float $lonTo): float
    {
        $dLat = deg2rad($latTo - $latFrom);
        $dLon = deg2rad($lonTo - $lonFrom);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * sin($dLon / 2) ** 2;

        return self::EARTH_RADIUS_METERS * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
    private function hasAttendance(User $user, Carbon $date): bool
    {
        return Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $date)
            ->exists();
    }

    private function lateDeadline(Carbon $reference): Carbon
    {
        return $reference->copy()->setTimeFromTimeString(self::LATE_AFTER);
    }

    /** @return Collection<int, array{id: int, name: string, lat: float, lng: float, radius: float}> */
    private function officesForMap(): Collection
    {
        return AttendanceSetting::where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn(AttendanceSetting $office) => [
                'id'     => $office->id,
                'name'   => $office->name,
                'lat'    => (float) $office->latitude,
                'lng'    => (float) $office->longitude,
                'radius' => (float) $office->radius,
            ])
            ->values();
    }

    private function initials(string $name): string
    {
        $initials = Str::of($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $initials ?: '-';
    }

    /**
     * Hari kerja (Senin-Jumat) tanpa catatan absensi, dihitung sejak mulai magang
     * sampai kemarin atau akhir periode magang, mana yang lebih dulu.
     *
     * @param  Collection<int, Attendance> $records catatan absensi bulan tersebut
     * @return list<Carbon>
     */
    private function missedWorkdays(User $user, ?string $firstDate, Carbon $monthStart, Carbon $monthEnd, Collection $records): array
    {
        $joinedAt = $user->periode_mulai?->copy()->startOfDay()
            ?? ($firstDate ? Carbon::parse($firstDate)->startOfDay() : null);

        if (! $joinedAt) {
            return [];
        }

        $from = $monthStart->copy()->max($joinedAt);
        $to   = $monthEnd->copy()->startOfDay()->min(today()->subDay());

        if ($user->periode_selesai) {
            $to = $to->min($user->periode_selesai->copy()->startOfDay());
        }

        $recorded = $records
            ->map(fn(Attendance $a) => $a->attendance_date->toDateString())
            ->all();

        $missed = [];

        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            if ($day->isWeekday() && ! in_array($day->toDateString(), $recorded, true)) {
                $missed[] = $day->copy();
            }
        }

        return $missed;
    }

    private function fail(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
    private function reject(string $message): never
    {
        throw new HttpResponseException($this->fail($message));
    }

    private function rejectInvalidPhoto(): never
    {
        $this->reject(sprintf(
            'Foto tidak valid atau lebih dari %d MB.',
            intdiv(self::MAX_PHOTO_BYTES, 1024 * 1024)
        ));
    }
}
