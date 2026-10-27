<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
            $table->dropConstrainedForeignId('ticket_id');
            $table->unique(['user_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'activity_date']);
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'approved', 'revision'])->default('draft');
            $table->index('status');
        });
    }
};