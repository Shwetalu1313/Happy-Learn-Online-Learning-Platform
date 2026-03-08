<?php

namespace App\Http\Controllers;

use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /* we can comment this construct cuz we use middle at web.php */
    //    public function __construct()
    //    {
    //        $this->middleware('auth');
    //    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $homeData = Cache::remember('home-page-payload-v1', now()->addMinutes(3), function () {
            return [
                'us_ex' => CurrencyExchange::getUSD(),
                'basicCourseEnum' => CourseTypeEnums::BASIC->value,
                'newCourses' => Course::getNewCourseLimitSix(),
                'popularCourses' => CourseEnrollUser::PopularCourses(),
                'studentCount' => User::where('role', UserRoleEnums::STUDENT->value)->count(),
                'titlePage' => 'home',
            ];
        });

        return view('home', $homeData);
    }
}
