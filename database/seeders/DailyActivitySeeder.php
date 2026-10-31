<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Data dummy untuk halaman Daily Activity.
 * Jalankan: php artisan db:seed --class=DailyActivitySeeder
 * Aman dijalankan berulang kali (data dummy lama dihapus dulu).
 */
class DailyActivitySeeder extends Seeder
{
    /** Insert 1 baris, otomatis membuang kolom yang tidak ada di tabel. */
    private function insertRow(string $table, array $row): int
    {
        $columns = array_flip(Schema::getColumnListing($table));

        return DB::table($table)->insertGetId(array_intersect_key($row, $columns));
    }

    /** Kalau kolom bertipe enum, kembalikan daftar nilainya. Kalau bukan, null. */
    private function enumValues(string $table, string $column): ?array
    {
        $row = DB::selectOne("SHOW COLUMNS FROM `{$table}` WHERE Field = ?", [$column]);

        if ($row && preg_match('/^enum\((.*)\)$/i', $row->Type, $m)) {
            return str_getcsv($m[1], ',', "'");
        }

        return null;
    }

    /** Pilih nilai pertama dari $candidates yang ada di enum. Kalau bukan enum, pakai $candidates[0]. */
    private function pick(?array $enum, array $candidates, string $what): string
    {
        if ($enum === null) {
            return $candidates[0];
        }

        foreach ($candidates as $c) {
            if (in_array($c, $enum, true)) {
                return $c;
            }
        }

        throw new \RuntimeException(
            "Kolom {$what} bertipe enum dengan nilai: " . implode(', ', $enum)
            . ". Tidak ada yang cocok dengan: " . implode(', ', $candidates)
        );
    }

    public function run(): void
    {
        $now = now();

        // Baca pilihan nilai dari database Anda
        $statusEnum   = $this->enumValues('tickets', 'status');
        $priorityEnum = $this->enumValues('tickets', 'priority');
        $categoryEnum = $this->enumValues('tickets', 'category');

        $statusDone   = $this->pick($statusEnum, ['completed', 'selesai', 'done', 'closed', 'verified'], 'tickets.status');
        $priorityHigh = $this->pick($priorityEnum, ['high', 'tinggi', 'urgent'], 'tickets.priority');
        $priorityNorm = $this->pick($priorityEnum, ['normal', 'medium', 'sedang', 'low', 'rendah'], 'tickets.priority');
        $category     = $categoryEnum[0] ?? 'Instalasi';

        $this->command?->info("Status selesai yang dipakai: {$statusDone}");

        // 1. Hapus data dummy lama
        $oldIds = DB::table('users')->where('email', 'like', '%@dummy.test')->pluck('id');
        DB::table('daily_activities')->whereIn('user_id', $oldIds)->delete();
        DB::table('tickets')->whereIn('user_id', $oldIds)->delete();
        DB::table('users')->whereIn('id', $oldIds)->delete();
        DB::table('tickets')->where('ticket_number', 'like', 'DMY-%')->delete();

        // 2. Mahasiswa dummy
        $students = [
            ['Refangga Ardiansah', '22053460012', 'Univ. Jember',             'Install & Aktivasi', 'Posko Jember Kota'],
            ['Dimas Pratama',      '22053460045', 'Politeknik Negeri Jember', 'Install & Aktivasi', 'Posko Jember Timur'],
            ['Fadhil Ramadhan',    '22053460088', 'Univ. Brawijaya',          'PM OSP',             'Posko Lumajang'],
            ['Siti Aisyah',        '22053460019', 'Telkom University',        'IT Support',         'Posko Gandul Pusat'],
            ['Annisa Safitri',     '22053460032', 'Universitas Indonesia',    'Install & Aktivasi', 'Posko Mampang'],
            ['Bagas Setiawan',     '22053460051', 'Univ. Jember',             'PM OSP',             'Posko Lumajang'],
            ['Nadia Putri',        '22053460066', 'Telkom University',        'IT Support',         'Posko Gandul Pusat'],
        ];

        $userIds = [];
        foreach ($students as [$name, $nim, $univ, $division, $posko]) {
            $userIds[$nim] = $this->insertRow('users', [
                'name'             => $name,
                'email'            => $nim . '@dummy.test',
                'password'         => Hash::make('password'),
                'role'             => 'user',
                'nim'              => $nim,
                'university'       => $univ,
                'asal_universitas' => $univ,
                'division'         => $division,
                'posko'            => $posko,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        // 3. Judul tiket per divisi
        $catalog = [
            'Install & Aktivasi' => [
                ['Splicing ODP-JBR-042', $priorityHigh],
                ['Pengukuran Redaman OPM', $priorityNorm],
                ['Instalasi ONT Pelanggan', $priorityNorm],
                ['Troubleshooting Redaman Tinggi', $priorityNorm],
            ],
            'PM OSP' => [
                ['Penarikan Dropcore 150m', $priorityNorm],
                ['Pemasangan Tiang Baru', $priorityHigh],
                ['Survey Rute Kabel', $priorityNorm],
            ],
            'IT Support' => [
                ['Patchcord & Cek Router Gandul', $priorityNorm],
                ['Konfigurasi Switch Lantai 2', $priorityHigh],
                ['Maintenance Server Harian', $priorityNorm],
            ],
        ];

        // 4. Tiket selesai: hari ini sampai 4 hari ke belakang.
        //    Setiap hari mahasiswa dan tiketnya sengaja BERBEDA supaya filter tanggal terlihat bekerja.
        $counter = 1;
        $worked  = []; // mahasiswa mana yang punya tiket di hari ke-berapa

        foreach ($students as $i => [$name, $nim, , $division]) {
            foreach (range(0, 4) as $daysAgo) {
                // Selang-seling: tiap hari ada mahasiswa yang tidak mengerjakan tiket
                if (($i + $daysAgo) % 3 === 2) {
                    continue;
                }

                $worked[$nim][$daysAgo] = true;

                $items  = array_values($catalog[$division]);
                $shift  = ($i + $daysAgo) % count($items);
                $items  = array_merge(array_slice($items, $shift), array_slice($items, 0, $shift));
                $total  = min(count($items), (($i + $daysAgo) % 3) + 1);

                $day  = $now->copy()->subDays($daysAgo)->startOfDay();
                $slot = $day->copy()->setTime(7, 45)->addMinutes(($i * 7 + $daysAgo * 5) % 40);

                foreach (array_slice($items, 0, $total) as [$title, $priority]) {
                    $started   = $slot->copy();
                    $completed = $slot->copy()->addMinutes(60 + (($counter * 17) % 90));
                    $slot      = $completed->copy()->addMinutes(15);
                    $number    = sprintf('TKT-%05d', $counter++);

                    $this->insertRow('tickets', [
                        'ticket_number' => $number,
                        'code'          => $number,
                        'user_id'       => $userIds[$nim],
                        'title'         => $title,
                        'description'   => $title,
                        'category'      => $category,
                        'priority'      => $priority,
                        'status'        => $statusDone,
                        'started_at'    => $started,
                        'completed_at'  => $completed,
                        'created_at'    => $started,
                        'updated_at'    => $completed,
                    ]);
                }
            }
        }

        // 5. Catatan logbook: hanya untuk hari yang ada tiketnya.
        //    Hari ini, mahasiswa urutan ganjil sengaja belum mengisi.
        foreach ($students as $i => [, $nim]) {
            foreach (range(0, 4) as $daysAgo) {
                if (empty($worked[$nim][$daysAgo])) {
                    continue;
                }
                if ($daysAgo === 0 && $i % 2 === 1) {
                    continue;
                }

                $this->insertRow('daily_activities', [
                    'user_id'       => $userIds[$nim],
                    'activity_date' => $now->copy()->subDays($daysAgo)->toDateString(),
                    'description'   => 'Menyelesaikan tiket lapangan sesuai jadwal, koordinasi dengan mentor, dan dokumentasi hasil pekerjaan.',
                    'status'        => 'draft',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        // 6. Tiket contoh untuk AKUN PESERTA ASLI (non-dummy), supaya Anda bisa login sendiri.
        //    Kode tiketnya diawali DMY-. Logbook sengaja tidak dibuat: isi sendiri lewat halaman Daily Activity peserta.
        $realUsers = DB::table('users')
            ->where(fn ($q) => $q->where('role', '!=', 'admin')->orWhereNull('role'))
            ->where('email', 'not like', '%@dummy.test')
            ->pluck('id');

        $demoItems   = array_values($catalog['PM OSP']);
        $demoCounter = 1;

        foreach ($realUsers as $uid) {
            foreach (range(0, 4) as $daysAgo) {
                $slot  = $now->copy()->subDays($daysAgo)->startOfDay()->setTime(8, 10);
                $total = ($daysAgo % 3) + 1; // 1-3 tiket per hari

                for ($k = 0; $k < $total; $k++) {
                    [$title, $priority] = $demoItems[($daysAgo + $k) % count($demoItems)];

                    $started   = $slot->copy();
                    $completed = $slot->copy()->addMinutes(75 + ($k * 20));
                    $slot      = $completed->copy()->addMinutes(15);
                    $number    = sprintf('DMY-%05d', $demoCounter++);

                    $this->insertRow('tickets', [
                        'ticket_number' => $number,
                        'code'          => $number,
                        'user_id'       => $uid,
                        'title'         => $title,
                        'description'   => $title,
                        'category'      => $category,
                        'priority'      => $priority,
                        'status'        => $statusDone,
                        'started_at'    => $started,
                        'completed_at'  => $completed,
                        'created_at'    => $started,
                        'updated_at'    => $completed,
                    ]);
                }
            }
        }
    }
}