<?php

namespace App\Http\Controllers;

use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\User;
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
            return view('users.TeacherDashboard', compact('titlePage', 'breadcrumbs', 'courses'));
        } else {
            return view('users.StudentDashboard', compact('titlePage', 'breadcrumbs'));
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

    public static function showCourses()
    {
        $titlePage = 'Courses';
        $us_ex = CurrencyExchange::getUSD();
        $basicCourseEnum = CourseTypeEnums::BASIC->value;

        $categories = Cache::remember('learner-course-categories-v1', now()->addMinutes(3), function () {
            return Category::query()
                ->select(['id', 'name'])
                ->with([
                    'sub_categories' => fn ($query) => $query
                        ->select(['id', 'name', 'category_id'])
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
        });

        return view('course.ListforLearners', compact('titlePage', 'us_ex', 'basicCourseEnum', 'categories'));
    }
}
