<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_enroll_users', function (Blueprint $table) {
            $table->string('card_last_four', 4)->nullable()->after('card_number');
        });

        DB::table('course_enroll_users')
            ->whereNotNull('card_number')
            ->update([
                'card_last_four' => DB::raw('RIGHT(card_number, 4)'),
            ]);

        DB::table('course_enroll_users')->update([
            'card_number' => null,
            'cvv' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enroll_users', function (Blueprint $table) {
            $table->dropColumn('card_last_four');
        });
    }
};
