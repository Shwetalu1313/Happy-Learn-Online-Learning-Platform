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
        if (! Schema::hasTable('notification_rules')) {
            Schema::create('notification_rules', function (Blueprint $table) {
                $table->id();
                $table->string('event_key')->unique();
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_enabled')->default(true);
                $table->json('channels')->nullable();
                $table->string('template_title')->nullable();
                $table->string('template_subject')->nullable();
                $table->text('template_line')->nullable();
                $table->string('template_action_text')->nullable();
                $table->string('template_end')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notification_rules')) {
            Schema::dropIfExists('notification_rules');
        }
    }
};
