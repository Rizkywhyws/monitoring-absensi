<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            // Check In
            $table->dateTime('check_in')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_in_accuracy', 8, 2)->nullable();
            $table->decimal('check_in_distance', 10, 2)->nullable();
            $table->string('check_in_photo', 500)->nullable();

            // Check Out
            $table->dateTime('check_out')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->decimal('check_out_accuracy', 8, 2)->nullable();
            $table->decimal('check_out_distance', 10, 2)->nullable();
            $table->string('check_out_photo', 500)->nullable();

            // Status
            $table->enum('status', [
                'hadir',
                'telat',
            ])->default('hadir');

            $table->timestamps();

            // Satu user hanya boleh punya satu absensi dalam satu hari
            $table->unique([
                'user_id',
                'attendance_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};