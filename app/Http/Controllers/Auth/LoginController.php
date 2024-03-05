<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnums;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected string $redirectTo = '/home'; // Default redirect path

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectTo(): string
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Check if the user is an admin
        if ($user && $user->role === UserRoleEnums::ADMIN) {
            return route('dashboard'); // Redirect admin to admin dashboard
        } else {
            return route('home'); // Redirect regular user to user dashboard
        }
    }
}
