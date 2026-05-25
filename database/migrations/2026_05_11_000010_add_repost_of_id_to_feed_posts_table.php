<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->foreignId('repost_of_id')
                  ->nullable()
                  ->after('image')
                  ->constrained('feed_posts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\FeedPost::class, 'repost_of_id');
            $table->dropColumn('repost_of_id');
        });
    }
};
