<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->string('template', 30)->default('livre')->after('message');
            $table->json('template_data')->nullable()->after('template');
            $table->text('private_note')->nullable()->after('template_data');
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropColumn(['template', 'template_data', 'private_note']);
        });
    }
};
