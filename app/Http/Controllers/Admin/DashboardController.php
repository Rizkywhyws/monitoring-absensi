<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private const ROLE_PESERTA = 'user';
    private const TICKET_DONE = [
        'done',
        'verified',
    ];
    private const TICKET_PROGRESS = [
        'to_do',
        'on_progress',
        'hold',
        'revision',
    ];
    private const WARNA = [
        [
            'bg-primary',
            'text-primary',
            'bg-primary text-on-primary',
        ],
        [
            'bg-secondary',
            'text-secondary',
            'bg-secondary text-on-secondary',
        ],
        [
            'bg-tertiary',
            'text-tertiary',
            'bg-tertiary text-on-tertiary',
        ],
        [
            'bg-secondary-container',
            'text-secondary',
            'bg-secondary-container text-on-secondary-container',
        ],
    ];

    public function index()
    {
        $hariIni = today();
        $aktif = fn () => User::query()
            ->where('role', self::ROLE_PESERTA)
            ->where('status', 'active')
            ->where(function ($q) use ($hariIni) {
                $q->whereNull('periode_selesai')
                    ->orWhereDate('periode_selesai', '>=', $hariIni);
            });
        $totalUser = User::query()
            ->where('role', self::ROLE_PESERTA)
            ->count();

        $userAktif = $aktif()->count();
        $logbookHariIni = DB::table('daily_activities')
            ->whereDate('activity_date', $hariIni)
            ->where('status', '!=', 'draft')
            ->count();
        $wajibAbsenQuery = $aktif()
            ->where(function ($q) use ($hariIni) {
                $q->whereNull('periode_mulai')
                    ->orWhereDate('periode_mulai', '<=', $hariIni);
            })
            ->select('id');

        $wajib = (clone $wajibAbsenQuery)->count();

        $absenPerStatus = DB::table('attendances')
            ->whereDate('attendance_date', $hariIni)
            ->whereNotNull('check_in')
            ->whereIn('user_id', $wajibAbsenQuery)
            ->selectRaw('status, COUNT(DISTINCT user_id) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($absenPerStatus['hadir'] ?? 0);
        $telat = (int) ($absenPerStatus['telat'] ?? 0);

        $sudahAbsen = $hadir + $telat;
        $geofence = DB::table('attendance_settings')
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        $absensi = [
            'wajib' => $wajib,
            'hadir' => $hadir,
            'telat' => $telat,
            'belum' => max($wajib - $sudahAbsen, 0),
            'sudah' => $sudahAbsen,
            'persen' => $wajib > 0
                ? (int) round(($sudahAbsen / $wajib) * 100)
                : 0,
            'radius' => $geofence
                ? (int) round($geofence->radius)
                : null,
        ];
        $ticketStats = DB::table('tickets')
            ->selectRaw(
                "
                SUM(
                    CASE
                        WHEN status IN (?, ?) THEN 1
                        ELSE 0
                    END
                ) as done,

                SUM(
                    CASE
                        WHEN status IN (?, ?, ?, ?) THEN 1
                        ELSE 0
                    END
                ) as progress
                ",
                [
                    ...self::TICKET_DONE,
                    ...self::TICKET_PROGRESS,
                ]
            )
            ->first();

        $ticketDone = (int) ($ticketStats->done ?? 0);
        $ticketProgress = (int) ($ticketStats->progress ?? 0);
        $semuaDivisi = User::query()
            ->where('role', self::ROLE_PESERTA)
            ->whereNotNull('division')
            ->where('division', '!=', '')
            ->distinct()
            ->orderBy('division')
            ->pluck('division');

        $warnaGrup = [];
        $jumlahWarna = count(self::WARNA);

        foreach ($semuaDivisi as $i => $nama) {
            $key = Str::lower($nama);

            $warnaGrup[$key] ??= count($warnaGrup) % $jumlahWarna;
        }

        $perDivisi = $aktif()
            ->whereNotNull('division')
            ->where('division', '!=', '')
            ->selectRaw(
                "
                division,
                COUNT(*) as total,
                GROUP_CONCAT(
                    DISTINCT NULLIF(mentor, '')
                    SEPARATOR ', '
                ) as mentors
                "
            )
            ->groupBy('division')
            ->orderBy('division')
            ->get();

        $groups = [];

        foreach ($perDivisi as $row) {
            if ($row->total < 1) {
                continue;
            }

            $warna = self::WARNA[$warnaGrup[Str::lower($row->division)] ?? 0];

            $groups[] = [
                $row->division,
                (int) $row->total,
                $row->mentors ?: '-',
                $warna[0],
                $warna[1],
            ];
        }

        $activities = $this->activityFeed($warnaGrup);

        return view(
            'admin.dashboardAdmin',
            compact(
                'totalUser',
                'userAktif',
                'logbookHariIni',
                'absensi',
                'ticketDone',
                'ticketProgress',
                'groups',
                'activities'
            )
        );
    }

    private function activityFeed(array $warnaGrup): array
    {
        $base = fn (string $table, string $alias) => DB::table("$table as $alias")
            ->join('users as u', 'u.id', '=', "$alias.user_id")
            ->where('u.role', self::ROLE_PESERTA);

        $events = collect();

        $base('attendances', 'a')
            ->whereNotNull('a.check_in')
            ->orderByDesc('a.check_in')
            ->limit(6)
            ->get([
                'u.name',
                'u.division as grup',
                'a.check_in as waktu',
                'a.check_in_distance as jarak',
            ])
            ->each(function ($r) use ($events) {
                $jarak = $r->jarak !== null
                    ? ' (' . (int) round($r->jarak) . 'm dari kantor)'
                    : '';

                $events->push([
                    $r,
                    'Melakukan Check-in Absensi' . $jarak,
                ]);
            });

        $base('tickets', 't')
            ->orderByDesc('t.updated_at')
            ->limit(6)
            ->get([
                'u.name',
                'u.division as grup',
                't.updated_at as waktu',
                't.ticket_number',
                't.title',
                't.status',
            ])
            ->each(function ($r) use ($events) {
                $ref = "{$r->ticket_number} ({$r->title})";

                $deskripsi = match ($r->status) {
                    'done',
                    'verified' =>
                        "Menyelesaikan Ticket {$ref}",

                    'revision' =>
                        "Ticket {$ref} perlu direvisi",

                    'cancelled' =>
                        "Ticket {$ref} dibatalkan",

                    'to_do' =>
                        "Menerima Ticket {$ref}",

                    default =>
                        "Update Progres Ticket {$ref}",
                };

                $events->push([
                    $r,
                    $deskripsi,
                ]);
            });

        $base('daily_activities', 'd')
            ->where('d.status', '!=', 'draft')
            ->orderByDesc('d.updated_at')
            ->limit(6)
            ->get([
                'u.name',
                'u.division as grup',
                'd.updated_at as waktu',
                'd.description',
            ])
            ->each(function ($r) use ($events) {
                $events->push([
                    $r,
                    'Submit Daily Activity Logbook (' .
                    Str::limit((string) $r->description, 80) .
                    ')',
                ]);
            });

        return $events
            ->filter(fn ($e) => $e[0]->waktu)
            ->sortByDesc(
                fn ($e) => Carbon::parse($e[0]->waktu)->timestamp
            )
            ->take(6)
            ->map(function ($e) use ($warnaGrup) {
                [$r, $deskripsi] = $e;

                $waktu = Carbon::parse($r->waktu);
                $key = Str::lower((string) $r->grup);
                $avatar = isset($warnaGrup[$key])
                    ? self::WARNA[$warnaGrup[$key]][2]
                    : 'bg-surface-container-highest text-on-surface';
                $inisial = Str::of($r->name)
                    ->explode(' ')
                    ->filter()
                    ->take(2)
                    ->map(
                        fn ($w) => Str::upper(
                            Str::substr($w, 0, 1)
                        )
                    )
                    ->implode('');
                $jam = $waktu->isToday()
                    ? $waktu->format('H:i')
                    : $waktu
                        ->locale('id')
                        ->translatedFormat('d M, H:i');

                return [
                    $inisial,
                    $r->name,
                    $r->grup ?: '-',
                    $jam,
                    $deskripsi,
                    $avatar,
                ];
            })
            ->values()
            ->all();
    }
}