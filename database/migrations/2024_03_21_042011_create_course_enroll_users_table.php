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
        Schema::create('course_enroll_users', function (Blueprint $table) {
            $table->uuid();
            $table->bigInteger('user_id');
            $table->bigInteger('course_id');
            $table->bigInteger('card_number');
            $table->string('expired_date');
            $table->string('cardHolderName');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enroll_users');
    }
};