<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('ticket_id')
                ->nullable()
                ->constrained('tickets')
                ->nullOnDelete();

            $table->date('activity_date');

            $table->text('description');

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'revision',
            ])->default('draft');

            $table->timestamps();

            // Index untuk pencarian berdasarkan tanggal dan status
            $table->index('activity_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};