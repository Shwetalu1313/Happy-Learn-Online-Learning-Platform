<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\CourseContributor;
use App\Models\SystemActivity;
use App\Models\User;
use App\Notifications\ContributorEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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

        if ($email === \auth()->user()->email){
            return redirect()->back()->with('error', 'You shared your own course. This action is not allowed.');
        }

        // Check if the user is a teacher
        if ($user->role->value !== UserRoleEnums::TEACHER->value) {
            return redirect()->back()->with('error', 'Unauthorized user! User should be a teacher.');
        }

        $courseID = $request->input('course_id');

        // Check if the user is already a contributor
        $existingContributor = CourseContributor::where([
            'user_id' => $user->id,
            'course_id' => $courseID,
        ])->exists();

        if ($existingContributor) {
            return redirect()->back()->with('error', 'This course is already shared with ' . $user->name);
        }

        // Create new course contributor
        $courseContributor = CourseContributor::create([
            'user_id' => $user->id,
            'course_id' => $courseID,
        ]);

        if ($courseContributor) {
            $mail = $user-> email;

            // Check if the email address is valid
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Invalid email address: ' . $mail);
            }

            $coursedata = Course::find($courseID);
            $courseName = $coursedata->title;
            $data = [
                'greeting' => 'Hello, '. $user->name,
                'line'=> \auth()->user()->name . ' gave you contributor access to '.$courseName.'.',
                'end' => 'Check out a new responsibility 🐌',
                'actionText' => 'Check Now',
                'actionUrl' => route('course.index'),
            ];

            $systemActivity = [
                'table_name' => CourseContributor::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A course('.$courseName.') is shared with '. $user->name . '.',
                'about' => Auth::user()->name . '('.auth()->id().') is shared a course('.$courseName.') to '. $user->name .'>>'. $user->mail .'.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            // Send email notification
            Notification::send($user, new ContributorEmailNotification($data));
            //Mail::to($mail)->send(new ContributorEmailNotification($data));

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
    public function destroy(string $id, Request $request)
    {
        $contributor = CourseContributor::findOrFail($id);
        $contributor_name = $contributor->user->name;
        $contributor_email = $contributor->user->mail;
        $course_name = $contributor->course->title;
        $contributor->delete();

        $systemActivity = [
            'table_name' => CourseContributor::getModelName(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'short' => $course_name . ' contributor permission was revoked from ' .$contributor_name . '.',
            'about' => Auth::user()->name . '('.auth()->id().') revoke a course('.$course_name.') contributor to '. $contributor_name.'>>'.$contributor_email .'.',
            'target' => UserRoleEnums::ADMIN,
            'route_name' => $request->route()->getName(),
        ];
        SystemActivity::createActivity($systemActivity);

        $data = [
            'greeting' => 'Hello, '. $contributor_name,
            'line'=> $contributor_name .'\'s contributor access permission has been revoked for \''.$course_name.'\' course.',
            'end' => 'Have a great day',
            'actionText' => 'Check Now',
            'actionUrl' => route('course.index'),
        ];

        // Send email notification
        Notification::send($contributor->user, new ContributorEmailNotification($data)); // Changed $contributor_mail to $contributor->user
        return redirect()->back()->with('success', $contributor_name .'\'s contributor access permission has been revoked for \''.$course_name.'\' course.');
    }

}
