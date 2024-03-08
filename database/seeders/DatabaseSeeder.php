<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRoleEnums;
use App\Models\Category;
use App\Models\SubCategory;
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
           'email' => 'testthemail2023@gmail.com',
           'phone' => '45454',
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
            'points' => '4341',
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
            'points' => '98745642',
            'avatar' => 'avatars/user.png',
            'role' => UserRoleEnums::TEACHER->value,
            'about' => 'I will teach for you 🤏🏻',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory(15)->create();
        JobPost::factory(10)->create();

        Category::factory()->create(['name' => 'Technology & Computer Science', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Business & Finance', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Arts & Design', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Science & Engineering', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Health & Medicine', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Language & Communication', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Humanities & Social Sciences', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Personal Development', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Career & Professional Growth', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Lifestyle & Hobbies', 'img_path' => 'cate/sample.jpg']);
        Category::factory()->create(['name' => 'Primary School (MM)', 'img_path' => 'cate/sample.jpg']);

        /*(1)*/
        SubCategory::factory()->create(['name' => 'Web Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Mobile App Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Data Science & Analytics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Cyber-security', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Programming Languages', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Software Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Game Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Artificial Intelligence & Machine Learning', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);
        SubCategory::factory()->create(['name' => 'Internet of Things (IoT)', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 1]);

        /*(2)*/
        SubCategory::factory()->create(['name' => 'Entrepreneurship', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Marketing & Advertising', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Finance & Accounting', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Management & Leadership', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Business Strategy', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Sales & Customer Service', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Project Management', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Economics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);
        SubCategory::factory()->create(['name' => 'Business Law', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 2]);

        /*(3)*/
        SubCategory::factory()->create(['name' => 'Graphic Design', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Digital Illustration', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Photography & Videography', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Animation & Motion Graphics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Interior Design', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Fashion Design', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Fine Arts', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Music & Audio Production', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);
        SubCategory::factory()->create(['name' => 'Performing Arts', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 3]);

        /*(4)*/
        SubCategory::factory()->create(['name' => 'Biology & Life Sciences', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Chemistry', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Physics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Environmental Science', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Civil Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Mechanical Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Electrical Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Aerospace Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);
        SubCategory::factory()->create(['name' => 'Chemical Engineering', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 4]);

        /*(5)*/
        SubCategory::factory()->create(['name' => 'Medicine & Surgery', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Nursing & Healthcare', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Nutrition & Dietetics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Pharmaceutical Sciences', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Healthcare Administration', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);

        /*(6)*/
        SubCategory::factory()->create(['name' => 'English Language', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Spanish Language', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Communication Skills', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Writing & Editing', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Public Speaking', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Translation & Interpretation', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Linguistics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Literature Studies', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);
        SubCategory::factory()->create(['name' => 'Technical Writing', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 6]);

        /*(7)*/
        SubCategory::factory()->create(['name' => 'History', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Philosophy', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Sociology', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Anthropology', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Political Science', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Cultural Studies', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);

        /*(8)*/
        SubCategory::factory()->create(['name' => 'Time Management', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Goal Setting & Productivity', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Emotional Intelligence', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Creativity & Innovation', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Critical Thinking', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Decision Making', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);

        /*(9)*/
        SubCategory::factory()->create(['name' => 'Resume Writing & Job Hunting', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Career Development & Planning', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Interview Skills & Techniques', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Networking & Relationship Building', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Leadership Development', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Workplace Communication', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Conflict Resolution', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Workplace Ethics', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);
        SubCategory::factory()->create(['name' => 'Diversity & Inclusion', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 9]);

        /*(10)*/
        SubCategory::factory()->create(['name' => 'Cooking & Culinary Arts', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Gardening & Horticulture', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'DIY & Home Improvement', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Fashion & Beauty', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $this->call(JobPosition::class);
    }
}
