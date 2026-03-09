<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('video_provider', 40)->nullable()->after('body');
            $table->string('video_source', 1000)->nullable()->after('video_provider');
            $table->string('video_id', 64)->nullable()->after('video_source');
            $table->unsignedInteger('video_start_at')->default(0)->after('video_id');
            $table->boolean('video_is_preview')->default(false)->after('video_start_at');

            $table->index(['video_provider', 'video_id'], 'lessons_video_provider_video_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_video_provider_video_id_index');
            $table->dropColumn([
                'video_provider',
                'video_source',
                'video_id',
                'video_start_at',
                'video_is_preview',
            ]);
        });
    }
};
