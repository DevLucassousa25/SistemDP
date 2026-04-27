<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dateTime('maintenance_start')->nullable()->after('status');
            $table->dateTime('maintenance_end')->nullable()->after('maintenance_start');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['maintenance_start', 'maintenance_end']);
        });
    }
};
