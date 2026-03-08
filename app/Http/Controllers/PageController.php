<?php

namespace App\Http\Controllers;

use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    protected static function jobformIntro()
    {
        $titlePage = __('jobapplication.job_intro_title');

        return view('job.intro', compact('titlePage'));
    }

    protected static function jobPost()
    {
        $titlePage = __('nav.j_post_f');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard'],
            ['name' => __('jobapplication.job')],
            ['link' => route('job.intro'), 'name' => 'Job Post', 'active' => true],
        ];

        return view('job.post', compact('titlePage', 'breadcrumbs'));
    }

    protected static function dashboard()
    {
        $titlePage = __('nav.dashboard');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard', 'active' => true],
        ];

        return view('dashboard', compact('titlePage', 'breadcrumbs'));
    }

    protected static function UserDashboard()
    {
        $titlePage = __('nav.dashboard');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard', 'active' => true],
        ];
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        if (Auth::user()->role->value === UserRoleEnums::TEACHER->value) {
            $courses->loadCount('lessons');
            $courses->load(['sub_category:id,name,category_id', 'sub_category.category:id,name']);
            return view('users.TeacherDashboard', compact('titlePage', 'breadcrumbs', 'courses'));
        } else {
            $enrollments = $user->enrollCourses()
                ->with([
                    'course' => fn ($query) => $query
                        ->select(['id', 'title', 'image', 'courseType', 'fees', 'state', 'sub_category_id', 'created_at'])
                        ->withCount('lessons')
                        ->with(['sub_category:id,name,category_id', 'sub_category.category:id,name']),
                ])
                ->latest()
                ->get();

            return view('users.StudentDashboard', compact('titlePage', 'breadcrumbs', 'enrollments'));
        }
    }

    public static function welcome()
    {
        $titlePage = __('nav.wel_pg');
        $categories = Cache::remember('welcome-categories-v1', now()->addMinutes(3), function () {
            return Category::query()
                ->with([
                    'sub_categories' => fn ($query) => $query
                        ->orderBy('name')
                        ->withCount('courses'),
                ])
                ->orderBy('name')
                ->get();
        });

        return view('welcome', compact('titlePage', 'categories'));
    }

    public static function TopPointsUserList()
    {
        $titlePage = __('nav.tpu');

        return view('users.topPointsUserList', compact('titlePage'));
    }

    public static function teacherLists()
    {
        $titlePage = __('nav.teacher_lst');

        return view('users.teacherList', compact('titlePage'));
    }

    public static function CourseEnroll(string $id)
    {
        $titlePage = __('course.label_enroll_pg');
        $course = Course::findOrFail($id);

        // Check if the user is already enrolled in the course
        $enrollment = CourseEnrollUser::where('user_id', auth()->id())
            ->where('course_id', $id)
            ->first();

        if ($enrollment) {
            // User is already enrolled, redirect to another route
            return redirect()->route('course.detail', $course->id);
        }

        // User is not enrolled, render the enrollment page
        return view('course.enroll.CourseEnrollPage', compact('titlePage', 'course'));
    }

    public static function courseDetail(string $id)
    {
        $titlePage = __('Detail');
        $course = Course::findOrFail($id);

        return view('course.enroll.courseDetail', compact('titlePage', 'course'));
    }

    public static function createQuestion()
    {
        $titlePage = __('exercise.pg_title');

        return view('exercise.createQuestion', compact('titlePage'));
    }

    public static function showAllActivities()
    {
        $titlePage = 'Activities Logs';

        return view('ActivityLog', compact('titlePage'));
    }

    public static function showCourses(Request $request)
    {
        $titlePage = 'Courses';
        $us_ex = CurrencyExchange::getUSD();
        $basicCourseEnum = CourseTypeEnums::BASIC->value;
        $categoryId = (int) $request->query('category', 0);
        $subCategoryId = (int) $request->query('sub_category', 0);

        $cacheKey = sprintf('learner-course-categories-v2-c%s-s%s', $categoryId ?: 'all', $subCategoryId ?: 'all');

        $categories = Cache::remember($cacheKey, now()->addMinutes(3), function () use ($categoryId, $subCategoryId) {
            $collection = Category::query()
                ->select(['id', 'name'])
                ->when($categoryId > 0, fn ($query) => $query->where('id', $categoryId))
                ->with([
                    'sub_categories' => fn ($subCategoryQuery) => $subCategoryQuery
                        ->select(['id', 'name', 'category_id'])
                        ->when($subCategoryId > 0, fn ($query) => $query->where('id', $subCategoryId))
                        ->orderBy('name')
                        ->with([
                            'courses' => fn ($courseQuery) => $courseQuery
                                ->select(['id', 'title', 'courseType', 'fees', 'state', 'createdUser_id', 'sub_category_id', 'created_at'])
                                ->has('lessons')
                                ->with([
                                    'creator:id,name,role',
                                    'contribute_courses' => fn ($contributorQuery) => $contributorQuery
                                        ->select(['id', 'course_id', 'user_id'])
                                        ->with(['user:id,name,role']),
                                ])
                                ->withCount('lessons')
                                ->orderBy('created_at', 'desc'),
                        ]),
                ])
                ->orderBy('name')
                ->get();

            return $collection
                ->map(function ($category) {
                    $category->setRelation(
                        'sub_categories',
                        $category->sub_categories->filter(fn ($subCategory) => $subCategory->courses->isNotEmpty())->values()
                    );

                    return $category;
                })
                ->filter(fn ($category) => $category->sub_categories->isNotEmpty())
                ->values();
        });

        return view('course.ListforLearners', compact('titlePage', 'us_ex', 'basicCourseEnum', 'categories', 'categoryId', 'subCategoryId'));
    }
}
