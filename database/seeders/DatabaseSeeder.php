<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRoleEnums;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseContributor;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\Exercise;
use App\Models\Forum;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\SystemActivity;
use App\Models\User;

use App\Models\JobPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Seed the application's database.
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
        ]);

        User::factory(70)->create();
        JobPost::factory(10)->create();

        Category::factory()->create(['name' => 'Technology & Computer Science', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Business & Finance', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Arts & Design', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Science & Engineering', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Health & Medicine', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Primary School (MM)', 'img_path' => 'cate/sample.jpg']);

        /*(1)*/
        SubCategory::factory()->create(['name' => 'Web Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Mobile App Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Data Science & Analytics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Cyber-security', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Artificial Intelligence & Machine Learning', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);

        /*(2)*/
        SubCategory::factory()->create(['name' => 'Entrepreneurship', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Marketing & Advertising', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);

        /*(3)*/
        SubCategory::factory()->create(['name' => 'Graphic Design', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Digital Illustration', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Photography & Videography', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);

        /*(4)*/
        SubCategory::factory()->create(['name' => 'Biology & Life Sciences', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Chemistry', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Physics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);

        /*(5)*/
        SubCategory::factory()->create(['name' => 'Medicine & Surgery', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Nursing & Healthcare', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $this->call(JobPosition::class);
        $i = 1;
        while ($i <= 20) {
            Course::factory()->create(['title' => 'Course ' . $i]);
            $i++;
        }

        CourseContributor::factory(15)->create();

        $lesson = 1;
        while ($lesson <= 40) {
            Lesson::factory()->create(['title' => 'Lesson ' . $lesson]);
            $lesson++;
        }

        CurrencyExchange::factory(1)->create();
        CourseEnrollUser::factory(50)->create();

        $exercise = 1;
        while ($exercise <= 45) {
            Exercise::factory()->create(['title' => 'Exercise ' . $exercise]);
            $exercise++;
        }

        Question::factory()->count(30)->create()->each(function ($question) {
            if ($question->question_type === 'multiple_choice') {
                // Create multiple answers for multiple-choice questions
                Answer::factory()->count(rand(2, 5))->create(['question_id' => $question->id]);
            } elseif ($question->question_type === 'true_or_false') {
                // Create two answers for true/false questions
                Answer::factory()->count(2)->create(['question_id' => $question->id]);
            } elseif ($question->question_type === 'blank') {
                // Create a single correct answer for blank questions
                Answer::factory()->create(['question_id' => $question->id, 'is_correct' => true]);
            }
        });

        Forum::factory(30)->create();
        Comment::factory(40)->create();

        SystemActivity::factory(70)->create();

    }
}
