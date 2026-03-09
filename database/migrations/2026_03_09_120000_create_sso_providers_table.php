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
        Schema::create('sso_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_key', 50)->unique();
            $table->string('display_name', 100);
            $table->string('driver', 50);
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('redirect_uri')->nullable();
            $table->json('scopes')->nullable();
            $table->string('tenant', 100)->nullable();
            $table->string('icon_class', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->index(['driver', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_providers');
    }
};
