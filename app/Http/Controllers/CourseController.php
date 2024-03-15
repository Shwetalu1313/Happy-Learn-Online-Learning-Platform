<?php

namespace App\Http\Controllers;

use App\Enums\CourseStateEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
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
        $courses = Course::getCoursesForUser($user);
        return view('course.courseList', compact('courses','titlePage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titlePage = __('course.entry_title');
        return view('course.courseEntry', compact('titlePage'));
    }
    public function ToUpdatePage(){
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
            'requirements' => 'required',
        ]);
        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('course','public');
        }
        Course::create([
           'title' => $data['name'],
            'description' => $data['requirements'],
            'image' => $img_path,
            'courseType' => $data['courseType'],
            'fees' => $data['fee'],
            'createdUser_id' => Auth::id(),
            'sub_category_id' => $data['sub_cate_select'],
        ]);
        return redirect()->back()->with('success', __('course.success_create_alert_msg'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $titlePage = __('course.title_update');
        $course = Course::find($id);
        return view('course.update', compact('course','titlePage'));
        //return redirect($this->ToUpdatePage())->with(['course'=>$course, 'titlePage'=> $titlePage]);
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
        $course->title = $data['name'];
        $course->description = $data['requirements'];
        $course->courseType = $data['courseType'];
        $course->fees = $data['fee'];
        $course->sub_category_id = $data['sub_cate_select'];

        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('course','public');
            $course->image = $img_path;
        }

        $course->save();

        return redirect()->back()->with('success', __('course.success_update_alert_msg'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->back()->with('success', $course->title.' is completely deleted.');
    }

    public function updateToApproveState(string $id) {
        $course = Course::findOrFail($id);
        $course->state = CourseStateEnums::APPROVED->value;
        $course->approvedUser_id = Auth::id();

        if ($course->save()) {
            return response()->json(['success' => true, 'message' => __('course.success_update_approve_alert_msg')]);
        } else {
            return response()->json(['success' => false, 'message' => __('course.success_error_approve_alert_msg')], 500);
        }
    }
}
