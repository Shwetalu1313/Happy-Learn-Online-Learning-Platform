<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\SystemActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as CurRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('users.user_lst');
        $users = User::all();
        return view('users.list', compact('titlePage','users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $titlePage = __('users.profile_dtl');
        return view('users.detail', compact('user', 'titlePage'));
    }
    public function showProfile($id)
    {
        $user = User::findOrFail($id);
        $titlePage = __('users.profile_dtl');
        return view('users.detail', compact('user', 'titlePage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Deleted successfully.');
    }

    public function roleUpdate(Request $request, $id){
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        if ($user->update($request->only(['role']))){
            $systemActivity = [
                'table_name' => User::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => $user->email .' role was updated to '. $request->role .'.' ,
                'about' => $user->email .' role was updated to '. $request->role .' by '. Auth::user()->email ,
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', __('jobapplication.job_update_alert'));
        }
        else return redirect()->back()->with('success','Fail to update role of User ('.$user->mail.')');
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about' => 'max:255',
        ]);
        //dd($validatedData);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }
        if (empty($validatedData)){
            $validatedData['about'] = '';
        }

        $user->update($validatedData);

        return redirect()->back()->with('success', __('jobapplication.job_update_alert'));
    }
    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about' => 'max:255',
        ]);
        //dd($validatedData);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;

        }
        if (empty($validatedData)){
            $validatedData['about'] = '';
        }
        $user->update($validatedData);

        $systemActivity = [
            'table_name' => User::getModelName(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'short' => 'Profile was updated.',
            'about' => 'Updated these data ',
            'target' => null,
            'route_name' => $request->route()->getName(),
        ];
        SystemActivity::createActivity($systemActivity);

        return redirect()->back()->with('success', __('jobapplication.job_update_alert'));
    }


    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|different:currentPassword',
            'renewPassword' => 'required|string|same:newPassword',
        ]);

        if (!Hash::check($request->currentPassword, $user->password)) {
            return redirect()->back()->withErrors(['currentPassword' => __('auth.fail')]);
        }

        $user->update(['password' => Hash::make($request->newPassword)]);

            $systemActivity = [
                'table_name' => User::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'Password Changed.',
                'about' => 'Password was changed by '.$user->name,
                'target' => null,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

        return redirect()->back()->with('success', 'Password changed successfully');
    }
    public function changeUserPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|different:currentPassword',
            'renewPassword' => 'required|string|same:newPassword',
        ]);

        if (!Hash::check($request->currentPassword, $user->password)) {
            return redirect()->back()->withErrors(['currentPassword' => __('auth.fail')]);
        }

        $user->update(['password' => Hash::make($request->newPassword)]);

        return redirect()->back()->with('success', 'Password changed successfully');
    }
}
