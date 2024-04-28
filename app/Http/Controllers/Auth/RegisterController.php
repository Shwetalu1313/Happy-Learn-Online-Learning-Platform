<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnums;
use App\Http\Controllers\Controller;
use App\Models\SystemActivity;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Image;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'avatar' => ['required', 'image', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     * @throws ValidationException
     */
    protected function create(array $data)
    {
        $validatedData = $this->validator($data)->validate();

        $avatarPath = $validatedData['avatar']->store('avatars', 'public', ['hashName' => 'avatar']);

        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'birthdate' => $data['birthdate'],
            'avatar' => $avatarPath,
            'password' => Hash::make($data['password']),
            'about' => '',
        ]);

//        if ($user){
//            $systemActivity = [
//                'table_name' => User::getModelName(),
//                'ip_address' => request()->getClientIp(),
//                'user_agent' => request()->userAgent(),
//                'user_id' => auth()->id() ?? 0,
//                'short' => 'New User Created.' ,
//                'about' => $user->email . ' is just created.' ,
//                'target' => null,
//                'route_name' => request()->route()->getName(),
//            ];
//            SystemActivity::createActivity($systemActivity);
//        }

        event(new Registered($user));
        // $this->sendEmailVerificationNotification($user);
        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */

     protected function registered(HttpRequest $request, $user)
     {
         return redirect('email/verify');
         //return redirect('/home');
         //return redirect('emails/notification');
     }

}
