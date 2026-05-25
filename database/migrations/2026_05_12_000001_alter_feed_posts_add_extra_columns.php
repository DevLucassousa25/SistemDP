<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->after('repost_of_id');
            $table->boolean('is_pinned')->default(false)->after('views_count');
            $table->timestamp('edited_at')->nullable()->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'is_pinned', 'edited_at']);
        });
    }
};
