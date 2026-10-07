<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('groups')->updateOrInsert(
            ['name' => 'Grup Uji Coba'],
            [
                'description' => 'Grup contoh untuk pengujian',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $groupId = DB::table('groups')->where('name', 'Grup Uji Coba')->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'peserta@test.com'],
            [
                'name' => 'Peserta Uji Coba',
                'nim' => '999999999999',
                'password' => Hash::make('password'),
                'role' => 'user',
                'group_id' => $groupId,
                'periode_mulai' => '2026-08-01',
                'periode_selesai' => '2026-09-30',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}