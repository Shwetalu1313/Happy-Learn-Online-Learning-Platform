<?php

namespace App\Http\Controllers;

use App\Enums\CourseStateEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\SystemActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('course.list_title');
        $user = \auth()->user();
        $courseIds = Course::getCoursesForUser($user)->pluck('id')->filter()->values();
        $courses = Course::query()
            ->with([
                'creator:id,name',
                'approver:id,name',
                'enrollCourses:id,course_id',
                'contribute_courses.user:id,name,email',
            ])
            ->whereIn('id', $courseIds)
            ->get()
            ->sortBy(fn (Course $course) => (int) $courseIds->search($course->id))
            ->values();

        return view('course.courseList', compact('courses', 'titlePage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titlePage = __('course.entry_title');

        return view('course.courseEntry', compact('titlePage'));
    }

    public function ToUpdatePage()
    {
        return view('course.update');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'courseType' => 'required',
            'fee' => 'required|integer',
            'sub_cate_select' => 'required|integer',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'requirements' => 'required|string',
        ]);
        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('course', 'public');
        }
        $course = Course::create([
            'title' => $data['name'],
            'description' => $data['requirements'],
            'image' => $img_path ?? 'course/sample.jpg',
            'courseType' => $data['courseType'],
            'fees' => $data['fee'],
            'createdUser_id' => Auth::id(),
            'sub_category_id' => $data['sub_cate_select'],
        ]);

        if ($course) {
            $systemActivity = [
                'table_name' => Course::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => $course->title.' was created.',
                'about' => $course->title.' was created by.'.Auth::user()->name.'.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', __('course.success_create_alert_msg'));
        } else {
            return redirect()->back()->with('success', __('Fail Course Creation.'));
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $titlePage = __('course.title_update');
        $course = Course::query()
            ->with([
                'creator:id,name,avatar',
                'approver:id,name',
                'sub_category:id,name',
                'contribute_courses.user:id,name,email',
            ])
            ->findOrFail($id);
        $this->authorizeCourseOwnerOrAdmin($course);

        return view('course.update', compact('course', 'titlePage'));
        // return redirect($this->ToUpdatePage())->with(['course'=>$course, 'titlePage'=> $titlePage]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'courseType' => 'required',
            'fee' => 'required|integer',
            'sub_cate_select' => 'required|integer',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'requirements' => 'required',
        ]);

        $course = Course::findOrFail($id);
        $this->authorizeCourseOwnerOrAdmin($course);
        $course->title = $data['name'];
        $course->description = $data['requirements'];
        $course->courseType = $data['courseType'];
        $course->fees = $data['fee'];
        $course->sub_category_id = $data['sub_cate_select'];

        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('course', 'public');
            $course->image = $img_path ?? 'course/sample.jpg';
        }

        if ($course->save()) {

            $systemActivity = [
                'table_name' => Course::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => $course->title.' was updated.',
                'about' => $course->title.' was updated by.'.Auth::user()->name.'.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', __('course.success_create_alert_msg'));

        } else {
            return redirect()->back()->with('success', __('Fail Course Update.'));
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwnerOrAdmin($course);

        if ($course->delete()) {

            $systemActivity = [
                'table_name' => Course::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => $course->title.' was deleted.',
                'about' => $course->title.' was deleted by.'.Auth::user()->name.'.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', $course->title.' is completely deleted.');
        }

    }

    public function updateToApproveState(string $id, Request $request)
    {
        $this->authorizeAdminOnly();
        $course = Course::findOrFail($id);
        $course->state = CourseStateEnums::APPROVED->value;
        $course->approvedUser_id = Auth::id();

        if ($course->save()) {
            $systemActivity = [
                'table_name' => Course::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => $course->title.' was confirmed.',
                'about' => $course->title.' was confirmed by.'.Auth::user()->name.'.',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return response()->json(['success' => true, 'message' => __('course.success_update_approve_alert_msg')]);
        } else {
            return response()->json(['success' => false, 'message' => __('course.success_error_approve_alert_msg')], 500);
        }
    }

    private function authorizeCourseOwnerOrAdmin(Course $course): void
    {
        $authUser = auth()->user();
        if (! $authUser) {
            abort(403, 'Unauthorized');
        }

        if ($authUser->role->value === UserRoleEnums::ADMIN->value) {
            return;
        }

        if ((int) $course->createdUser_id === (int) $authUser->id) {
            return;
        }

        abort(403, 'You are not allowed to modify this course.');
    }

    private function authorizeAdminOnly(): void
    {
        $authUser = auth()->user();
        if (! $authUser || $authUser->role->value !== UserRoleEnums::ADMIN->value) {
            abort(403, 'Admin permission required.');
        }
    }
}
