<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToProvider($service){
        return Socialite::driver($service)->redirect();
    }

    public function handleProviderCallback($service){
        $user = Socialite::driver($service) ->user();

        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            Auth::login($existingUser, true);
        }
        else {
            $newUser = new User;
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->google_id = $user->id;
            $newUser->avatar = $user->getAvatar();
            $newUser->save();
            Auth::login($newUser,true);
            
        }
    }
}
