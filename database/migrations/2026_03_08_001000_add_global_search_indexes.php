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
        Schema::table('courses', function (Blueprint $table) {
            $table->index('title', 'courses_title_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name', 'categories_name_idx');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->index('name', 'sub_categories_name_idx');
        });

        Schema::table('job_posts', function (Blueprint $table) {
            $table->index('title', 'job_posts_title_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('name', 'users_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_idx');
        });

        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropIndex('job_posts_title_idx');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropIndex('sub_categories_name_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_name_idx');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_title_idx');
        });
    }
};
