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
            $table->uuid('id')->primary();
            $table->bigInteger('user_id');
            $table->bigInteger('course_id');
            $table->integer('amount')->nullable();
            $table->integer('receive_amount')->default(0);
            $table->string('payment_type')->nullable();
            $table->bigInteger('card_number')->nullable();
            $table->string('expired_date')->nullable();
            $table->integer('cvv')->nullable();
            $table->string('cardHolderName')->nullable();
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
