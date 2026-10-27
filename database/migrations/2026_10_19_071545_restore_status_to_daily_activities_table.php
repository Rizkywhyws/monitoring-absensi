<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('daily_activities', 'status')) {
                $table->enum('status', ['draft', 'submitted', 'approved', 'revision'])
                    ->default('draft');
                $table->index('status');
            }

            if (! Schema::hasColumn('daily_activities', 'ticket_id')) {
                $table->foreignId('ticket_id')->nullable()
                    ->constrained('tickets')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // dikosongkan sengaja
    }
};