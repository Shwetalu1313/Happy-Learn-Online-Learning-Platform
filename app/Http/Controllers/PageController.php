<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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

    public static function welcome(){
        $titlePage = __('nav.wel_pg');
        $categories = Category::orderBy('name')->get();
        return view('welcome', compact('titlePage', 'categories'));
    }


}
