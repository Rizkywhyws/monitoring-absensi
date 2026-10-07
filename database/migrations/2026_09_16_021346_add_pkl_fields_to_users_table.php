<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('asal_universitas', 150)->nullable()->after('nim');
            $table->string('email_pribadi', 150)->nullable()->unique()->after('email');
            $table->string('email_korporat', 150)->nullable()->unique()->after('email_pribadi');
            $table->string('nomor_telepon', 20)->nullable()->after('email_korporat');
            $table->string('unit_rayon', 50)->nullable()->after('status');
        });

        // Tambahkan 'peserta_pkl' ke enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin','peserta_pkl') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'asal_universitas',
                'email_pribadi',
                'email_korporat',
                'nomor_telepon',
                'unit_rayon',
            ]);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user'");
    }
};