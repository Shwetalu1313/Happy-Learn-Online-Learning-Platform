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
        if (! Schema::hasColumn('comments', 'parent_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('forum_id');
                $table->index('parent_id', 'comments_parent_id_index');
                $table->foreign('parent_id', 'comments_parent_id_foreign')
                    ->references('id')
                    ->on('comments')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('comments', 'parent_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign('comments_parent_id_foreign');
                $table->dropIndex('comments_parent_id_index');
                $table->dropColumn('parent_id');
            });
        }
    }
};
