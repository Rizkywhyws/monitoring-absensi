<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim', 30)->nullable()->after('name');

            $table->enum('role', ['user', 'admin'])
                ->default('user')
                ->after('password');

            $table->foreignId('group_id')
                ->nullable()
                ->after('role')
                ->constrained('groups')
                ->nullOnDelete();

            $table->date('periode_mulai')
                ->nullable()
                ->after('group_id');

            $table->date('periode_selesai')
                ->nullable()
                ->after('periode_mulai');

            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->after('periode_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);

            $table->dropColumn([
                'nim',
                'role',
                'group_id',
                'periode_mulai',
                'periode_selesai',
                'status',
            ]);
        });
    }
};