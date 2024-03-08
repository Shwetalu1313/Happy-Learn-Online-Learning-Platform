<?php

use App\Enums\UserRoleEnums;
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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->required()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->required()->nullable();
            $table->date('birthdate');
            $table->integer('points')->default(0);
            $table->string('avatar')->default('user.png');
            $table->string('role')->default(UserRoleEnums::STUDENT->value);
            $table->string('about')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
