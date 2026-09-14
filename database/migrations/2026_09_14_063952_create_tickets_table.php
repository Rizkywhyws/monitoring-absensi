<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number', 30)->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title', 150);

            $table->text('description');

            $table->string('category', 50);

            $table->enum('priority', [
                'low',
                'medium',
                'high',
            ])->default('medium');

            $table->enum('status', [
                'to_do',
                'on_progress',
                'hold',
                'revision',
                'done',
                'verified',
                'cancelled',
            ])->default('to_do');

            $table->dateTime('completed_at')->nullable();

            $table->dateTime('verified_at')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('revision_note')->nullable();

            $table->timestamps();

            // Index untuk mempercepat pencarian/filter
            $table->index('status');
            $table->index('priority');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};