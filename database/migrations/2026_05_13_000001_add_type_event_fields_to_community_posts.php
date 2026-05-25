<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->enum('type', ['post', 'aviso', 'evento', 'discussao'])->default('post')->after('content');
            $table->string('event_location', 200)->nullable()->after('type');
            $table->dateTime('event_date')->nullable()->after('event_location');
            $table->dateTime('event_ends_at')->nullable()->after('event_date');
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn(['type', 'event_location', 'event_date', 'event_ends_at']);
        });
    }
};
