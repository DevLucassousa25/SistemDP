<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('recurrence')->nullable()->after('completed_at');       // daily|weekly|monthly|yearly
            $table->date('recurrence_ends_at')->nullable()->after('recurrence');
            $table->unsignedSmallInteger('estimated_minutes')->nullable()->after('recurrence_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'recurrence_ends_at', 'estimated_minutes']);
        });
    }
};
