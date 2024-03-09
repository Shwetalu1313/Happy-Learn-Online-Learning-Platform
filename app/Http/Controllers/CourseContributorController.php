<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\CourseContributor;
use App\Models\ScheduleUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'exists:users,email'], // Validate email existence directly in the validation rules
        ]);

        $email = $request->input('email');

        // Retrieve the user with the provided email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No user found with the provided email');
        }

        // Check if the user is a teacher
        if ($user->role->value !== UserRoleEnums::TEACHER->value) {
            return redirect()->back()->with('error', 'Unauthorized user! User should be a teacher.');
        }

        $schedule_id = $request->input('course_id');

        // Check if the user is already a contributor
        $existingContributor = CourseContributor::where([
            'user_id' => $user->id,
            'course_id' => $schedule_id,
        ])->exists();

        if ($existingContributor) {
            return redirect()->back()->with('error', 'This course is already shared with ' . $user->name);
        }

        // Create new course contributor
        $courseContributor = CourseContributor::create([
            'user_id' => $user->id,
            'course_id' => $schedule_id,
        ]);

        if ($courseContributor) {
            return redirect()->back()->with('success', 'This course is shared with ' . $user->name);
        }

        return redirect()->back()->with('error', 'Failed to share course with ' . $user->name);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contributor = CourseContributor::findOrFail($id);
        $contributor_name = $contributor->user->name;
        $course_name = $contributor->course->title;
        $contributor->delete();
        return redirect()->back()->with('success', $contributor_name .'\'s contributor access permission has been revoked for \''.$course_name.'\'.');
    }
}
