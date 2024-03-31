<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnums;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    private static string $password;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Authorize User',
            'email' => 'admin@example.com',
            'phone' => '49563029',
            'birthdate' => fake()->date,
            'points' => '987',
            'avatar' => 'avatars/user.png',
            'role' => UserRoleEnums::ADMIN->value,
            'about' => 'I will control you 🤏🏻',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'phone' => '09454545454',
            'birthdate' => fake()->date,
            'points' => '120',
            'avatar' => 'avatars/user.png',
            'role' => UserRoleEnums::STUDENT->value,
            'about' => 'I will learn from you 🤏🏻',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);

        User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'phone' => '09454545454',
            'birthdate' => fake()->date,
            'points' => '300',
            'avatar' => 'avatars/user.png',
            'role' => UserRoleEnums::TEACHER->value,
            'about' => 'I will teach for you 🤏🏻',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);

        User::factory(70)->create();
    }
}
