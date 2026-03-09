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
        Schema::table('users', function (Blueprint $table) {
            $table->string('oauth_provider', 50)->nullable()->after('email');
            $table->string('oauth_provider_id')->nullable()->after('oauth_provider');
            $table->index('oauth_provider');
            $table->index('oauth_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['oauth_provider']);
            $table->dropIndex(['oauth_provider_id']);
            $table->dropColumn(['oauth_provider', 'oauth_provider_id']);
        });
    }
};
