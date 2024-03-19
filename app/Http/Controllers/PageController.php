<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRoleEnums;

class PageController extends Controller
{
    protected static function jobformIntro(){
        $titlePage = __('jobapplication.job_intro_title');

        return view('job.intro',compact('titlePage'));
    }

    protected static function jobPost(){
        $titlePage = __('nav.j_post_f');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard'],
            ['name' => __('jobapplication.job')],
            ['link' => route('job.intro'), 'name'=>'Job Post', 'active' => true],
        ];

        return view('job.post', compact('titlePage','breadcrumbs'));
    }

    protected static function dashboard(){
        $titlePage = __('nav.dashboard');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard', 'active' => true]
        ];

        return view('dashboard', compact('titlePage', 'breadcrumbs'));
    }

    protected static function UserDashboard(){
        $titlePage = __('nav.dashboard');
        $breadcrumbs = [
            ['link' => route('dashboard'), 'name' => 'Dashboard', 'active' => true]
        ];
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        if (Auth::user()->role->value === UserRoleEnums::TEACHER->value){
            return view('users.TeacherDashboard', compact('titlePage', 'breadcrumbs','courses'));
        }
        else{
            return view('users.StudentDashboard', compact('titlePage', 'breadcrumbs'));
        }
    }

    public static function welcome(){
        $titlePage = __('nav.wel_pg');
        $categories = Category::orderBy('name')->get();
        return view('welcome', compact('titlePage', 'categories'));
    }


}
