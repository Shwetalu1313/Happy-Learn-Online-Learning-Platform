<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*we can comment this construct cuz we use middle at web.php*/
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
//        if (Auth::user()->role === UserRoleEnums::ADMIN->value){
//            view('dashboard');
//        }
        return view('home');
        //dd(Auth::user()->id);
    }

}
