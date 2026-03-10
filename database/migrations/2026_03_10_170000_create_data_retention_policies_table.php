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
        if (! Schema::hasTable('data_retention_policies')) {
            Schema::create('data_retention_policies', function (Blueprint $table) {
                $table->id();
                $table->string('target_key', 80)->unique();
                $table->unsignedInteger('keep_days')->default(90);
                $table->unsignedInteger('archive_grace_days')->default(30);
                $table->boolean('exclude_unread_notifications')->default(true);
                $table->boolean('is_enabled')->default(true);
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_retention_policies')) {
            Schema::dropIfExists('data_retention_policies');
        }
    }
};
