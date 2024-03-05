<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

use App\Models\JobPost;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
        SubCategory::factory()->create(['name' => 'Public Health', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Mental Health & Psychology', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Fitness & Exercise Science', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
        SubCategory::factory()->create(['name' => 'Alternative Medicine', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 5]);
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
        SubCategory::factory()->create(['name' => 'Archaeology', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Geography', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Religious Studies', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);
        SubCategory::factory()->create(['name' => 'Cultural Studies', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 7]);

        /*(8)*/
        SubCategory::factory()->create(['name' => 'Time Management', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Goal Setting & Productivity', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Emotional Intelligence', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Stress Management', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Mindfulness & Meditation', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
        SubCategory::factory()->create(['name' => 'Self-Esteem & Confidence Building', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 8]);
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
        SubCategory::factory()->create(['name' => 'Travel & Adventure', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Arts & Crafts', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Pet Care & Training', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Sports & Fitness Activities', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Gaming & Entertainment', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);
        SubCategory::factory()->create(['name' => 'Fashion & Beauty', 'img_path' => 'cate/sub_cate/sample.jpg', 'category_id' => 10]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $this->call(JobPosition::class);
    }
}
