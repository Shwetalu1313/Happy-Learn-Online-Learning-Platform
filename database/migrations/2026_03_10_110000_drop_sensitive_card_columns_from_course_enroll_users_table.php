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
        Schema::table('course_enroll_users', function (Blueprint $table) {
            if (Schema::hasColumn('course_enroll_users', 'card_number')) {
                $table->dropColumn('card_number');
            }

            if (Schema::hasColumn('course_enroll_users', 'cvv')) {
                $table->dropColumn('cvv');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enroll_users', function (Blueprint $table) {
            if (! Schema::hasColumn('course_enroll_users', 'card_number')) {
                $table->bigInteger('card_number')->nullable()->after('payment_type');
            }

            if (! Schema::hasColumn('course_enroll_users', 'cvv')) {
                $table->integer('cvv')->nullable()->after('expired_date');
            }
        });
    }
};
