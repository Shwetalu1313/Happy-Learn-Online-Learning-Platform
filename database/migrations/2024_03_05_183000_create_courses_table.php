<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CourseTypeEnums;
use App\Enums\CourseStateEnums;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->char('courseType')->default(CourseTypeEnums::BASIC->value);
            $table->integer('fees')->default(0);
            $table->char('state')->default(CourseStateEnums::PENDING->value);
            $table->unsignedBigInteger('createdUser_id');
            $table->unsignedBigInteger('approvedUser_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
