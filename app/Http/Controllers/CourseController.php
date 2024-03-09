<?php

namespace App\Http\Controllers;

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
        if (Auth::user()->role->value != UserRoleEnums::ADMIN->value){
            $courses = $user->courses;
        }
        else {
            $courses = Course::all();
        }
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

        dd($id);
    }
}
