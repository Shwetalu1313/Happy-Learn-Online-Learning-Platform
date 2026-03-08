<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\CourseContributor;
use App\Models\SystemActivity;
use App\Models\User;
use App\Services\NotificationManager;
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
    public function store(Request $request, NotificationManager $notificationManager)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $email = $request->input('email');

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->back()->with('error', 'No user found with the provided email');
        }

        if ($email === auth()->user()->email) {
            return redirect()->back()->with('error', 'You shared your own course. This action is not allowed.');
        }

        if ($user->role->value !== UserRoleEnums::TEACHER->value) {
            return redirect()->back()->with('error', 'Unauthorized user! User should be a teacher.');
        }

        $courseID = $request->input('course_id');

        $existingContributor = CourseContributor::where([
            'user_id' => $user->id,
            'course_id' => $courseID,
        ])->exists();

        if ($existingContributor) {
            return redirect()->back()->with('error', 'This course is already shared with ' . $user->name);
        }

        $courseContributor = CourseContributor::create([
            'user_id' => $user->id,
            'course_id' => $courseID,
        ]);

        if ($courseContributor) {
            $courseData = Course::findOrFail($courseID);
            $courseName = $courseData->title;

            $systemActivity = [
                'table_name' => CourseContributor::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A course(' . $courseName . ') is shared with ' . $user->name . '.',
                'about' => Auth::user()->name . '(' . auth()->id() . ') is shared a course(' . $courseName . ') to ' . $user->name . '>>' . $user->email . '.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            $notificationManager->notifyContributorShared($user, auth()->user(), $courseData);

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
    public function destroy(string $id, Request $request, NotificationManager $notificationManager)
    {
        $contributor = CourseContributor::findOrFail($id);
        $contributorName = $contributor->user->name;
        $contributorEmail = $contributor->user->email;
        $courseName = $contributor->course->title;

        $systemActivity = [
            'table_name' => CourseContributor::getModelName(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'short' => $courseName . ' contributor permission was revoked from ' . $contributorName . '.',
            'about' => Auth::user()->name . '(' . auth()->id() . ') revoke a course(' . $courseName . ') contributor to ' . $contributorName . '>>' . $contributorEmail . '.',
            'target' => UserRoleEnums::ADMIN,
            'route_name' => $request->route()->getName(),
        ];
        SystemActivity::createActivity($systemActivity);

        $notificationManager->notifyContributorRevoked($contributor->user, auth()->user(), $contributor->course);
        $contributor->delete();

        return redirect()->back()->with('success', $contributorName . '\'s contributor access permission has been revoked for \'' . $courseName . '\' course.');
    }
}
