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
        if (! Schema::hasTable('data_purge_runs')) {
            Schema::create('data_purge_runs', function (Blueprint $table) {
                $table->id();
                $table->string('run_type', 40);
                $table->string('status', 40)->default('started');
                $table->unsignedBigInteger('triggered_by')->nullable();
                $table->json('summary')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();

                $table->index(['run_type', 'status'], 'dpr_run_type_status_idx');
                $table->index('started_at', 'dpr_started_at_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_purge_runs')) {
            Schema::dropIfExists('data_purge_runs');
        }
    }
};
