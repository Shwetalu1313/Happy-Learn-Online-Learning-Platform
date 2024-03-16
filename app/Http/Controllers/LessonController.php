<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('lesson.title');
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        return view('lesson.index', compact('titlePage', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titlePage = __('lesson.title');
        return view('lesson.lessonEntry',compact('titlePage'));
    }

    public function createForm($course_id)
    {
        $titlePage = __('lesson.title');
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        return view('lesson.lessonEntry',compact('titlePage','course_id','courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'requirements' => 'required|string',
            'course' => 'required|integer',
        ]);

        $done = Lesson::create([
            'title' => $validateData['title'],
            'body' => $validateData['requirements'],
            'creator_id' => \auth()->id(),
            'course_id' => $validateData['course'],
        ]);

        if ($done){
            return redirect(route('lesson.index'))->with('success', 'You created a new lesson');
        }
        else {
            return redirect()->back()->with('error', 'Fail to create a new lesson');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showAtAdmin(string $id){
        $lesson = Lesson::find($id);
        $titlePage = 'Review for ' . $lesson->title;
        return view('lesson.lessonDetailManage', compact('lesson','titlePage'));
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
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'requirements' => 'required|string',
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->title = $validateData['title'];
        $lesson->body =  $validateData['requirements'];

        if ($lesson->save()){
            return redirect()->back()->with('success', 'You updated! '. $lesson->title);
        }
        else {
            return redirect()->back()->with('error', 'It isn\'t able to update the data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
