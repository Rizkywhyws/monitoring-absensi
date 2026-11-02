<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE attendances
                MODIFY check_in           DATETIME      NULL,
                MODIFY check_in_latitude  DECIMAL(10,7) NULL,
                MODIFY check_in_longitude DECIMAL(10,7) NULL,
                MODIFY check_in_accuracy  DECIMAL(8,2)  NULL,
                MODIFY check_in_distance  DECIMAL(10,2) NULL,
                MODIFY check_in_photo     VARCHAR(500)  NULL,
                MODIFY status ENUM('hadir','telat','sakit','izin') NOT NULL DEFAULT 'hadir'
        ");

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('leave_request_id')
                ->nullable()
                ->after('status')
                ->constrained('leave_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leave_request_id');
        });

        DB::table('attendances')->whereIn('status', ['sakit', 'izin'])->delete();

        DB::statement("
            ALTER TABLE attendances
                MODIFY check_in           DATETIME      NOT NULL,
                MODIFY check_in_latitude  DECIMAL(10,7) NOT NULL,
                MODIFY check_in_longitude DECIMAL(10,7) NOT NULL,
                MODIFY check_in_accuracy  DECIMAL(8,2)  NOT NULL,
                MODIFY check_in_distance  DECIMAL(10,2) NOT NULL,
                MODIFY check_in_photo     VARCHAR(500)  NOT NULL,
                MODIFY status ENUM('hadir','telat') NOT NULL
        ");
    }
};