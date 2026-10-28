<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'university')) {
                $table->string('university')->nullable();
            }
            if (! Schema::hasColumn('users', 'division')) {
                $table->string('division')->nullable();
            }
            if (! Schema::hasColumn('users', 'posko')) {
                $table->string('posko')->nullable();
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('tickets', 'code')) {
                $table->string('code')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'priority')) {
                $table->string('priority')->default('normal'); // normal | high
            }
            if (! Schema::hasColumn('tickets', 'status')) {
                $table->string('status')->default('open');     // open | in_progress | completed
            }
            if (! Schema::hasColumn('tickets', 'started_at')) {
                $table->timestamp('started_at')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Sengaja kosong: kolom bisa saja sudah ada sebelum migration ini.
    }
};