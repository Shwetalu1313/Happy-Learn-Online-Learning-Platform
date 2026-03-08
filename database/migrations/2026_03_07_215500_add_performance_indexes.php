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
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['sub_category_id', 'created_at']);
            $table->index(['state', 'created_at']);
            $table->index('createdUser_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->index('course_id');
        });

        Schema::table('course_contributors', function (Blueprint $table) {
            $table->index(['course_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::table('course_enroll_users', function (Blueprint $table) {
            $table->index(['user_id', 'course_id']);
            $table->index(['course_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enroll_users', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'course_id']);
            $table->dropIndex(['course_id', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('course_contributors', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['sub_category_id', 'created_at']);
            $table->dropIndex(['state', 'created_at']);
            $table->dropIndex(['createdUser_id']);
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });
    }
};
