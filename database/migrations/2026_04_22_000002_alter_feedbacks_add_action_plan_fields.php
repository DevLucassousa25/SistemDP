<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->date('action_plan_deadline')->nullable()->after('action_plan');
            $table->foreignId('action_plan_responsible_id')
                ->nullable()
                ->after('action_plan_deadline')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['action_plan_responsible_id']);
            $table->dropColumn(['action_plan_deadline', 'action_plan_responsible_id']);
        });
    }
};
