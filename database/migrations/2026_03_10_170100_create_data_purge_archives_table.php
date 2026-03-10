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
        if (! Schema::hasTable('data_purge_archives')) {
            Schema::create('data_purge_archives', function (Blueprint $table) {
                $table->id();
                $table->string('target_key', 80);
                $table->string('source_pk', 120);
                $table->json('payload');
                $table->timestamp('source_created_at')->nullable();
                $table->timestamp('archived_at');
                $table->timestamp('purge_after_at');
                $table->timestamps();

                $table->unique(['target_key', 'source_pk'], 'dpa_target_source_unique');
                $table->index(['target_key', 'purge_after_at'], 'dpa_target_purge_after_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_purge_archives')) {
            Schema::dropIfExists('data_purge_archives');
        }
    }
};
