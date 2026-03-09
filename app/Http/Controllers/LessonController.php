<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonVideoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class LessonController extends Controller
{
    public function __construct(private LessonVideoService $lessonVideoService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $titlePage = __('lesson.title');
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);

        return view('lesson.index', compact('titlePage', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $titlePage = __('lesson.title');
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        $course_id = null;
        $videoProviders = $this->lessonVideoService->providerKeys();

        return view('lesson.lessonEntry', compact('titlePage', 'videoProviders', 'courses', 'course_id'));
    }

    public function createForm($course_id): View
    {
        $titlePage = __('lesson.title');
        $user = \auth()->user();
        $courses = Course::getCoursesForUser($user);
        $videoProviders = $this->lessonVideoService->providerKeys();

        return view('lesson.lessonEntry', compact('titlePage', 'course_id', 'courses', 'videoProviders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if requirements field is empty or contains only HTML tags
        $requirements = $request->input('requirements');
        if (empty($requirements) || strip_tags($requirements) === '') {
            $request->merge(['requirements' => null]);
        }

        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'requirements' => 'required|string',
            'course' => 'required|integer',
            'video_provider' => 'nullable|string|in:'.implode(',', $this->lessonVideoService->providerKeys()),
            'video_source' => 'nullable|string|max:1000',
            'video_start_at' => 'nullable|integer|min:0|max:86400',
            'video_is_preview' => 'nullable|boolean',
        ]);

        try {
            $videoPayload = $this->lessonVideoService->buildPayload(
                $validateData['video_provider'] ?? null,
                $validateData['video_source'] ?? null,
                isset($validateData['video_start_at']) ? (int) $validateData['video_start_at'] : null,
                $request->boolean('video_is_preview')
            );
        } catch (InvalidArgumentException $invalidArgumentException) {
            return redirect()->back()
                ->withErrors(['video_source' => $invalidArgumentException->getMessage()])
                ->withInput();
        }

        $done = Lesson::create([
            'title' => $validateData['title'],
            'body' => $validateData['requirements'],
            'creator_id' => \auth()->id(),
            'course_id' => $validateData['course'],
            ...$videoPayload,
        ]);

        if ($done) {
            return redirect(route('lesson.index'))->with('success', 'You created a new lesson for'.$done->course->title);
        } else {
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

    public function showAtAdmin(string $id): View
    {
        $lesson = Lesson::findOrFail($id);
        $titlePage = 'Review for '.$lesson->title;
        $videoProviders = $this->lessonVideoService->providerKeys();

        return view('lesson.lessonDetailManage', compact('lesson', 'titlePage', 'videoProviders'));
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
    public function update(Request $request, string $id): RedirectResponse
    {
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'requirements' => 'required|string',
            'video_provider' => 'nullable|string|in:'.implode(',', $this->lessonVideoService->providerKeys()),
            'video_source' => 'nullable|string|max:1000',
            'video_start_at' => 'nullable|integer|min:0|max:86400',
            'video_is_preview' => 'nullable|boolean',
        ]);

        try {
            $videoPayload = $this->lessonVideoService->buildPayload(
                $validateData['video_provider'] ?? null,
                $validateData['video_source'] ?? null,
                isset($validateData['video_start_at']) ? (int) $validateData['video_start_at'] : null,
                $request->boolean('video_is_preview')
            );
        } catch (InvalidArgumentException $invalidArgumentException) {
            return redirect()->back()
                ->withErrors(['video_source' => $invalidArgumentException->getMessage()])
                ->withInput();
        }

        $lesson = Lesson::findOrFail($id);
        $lesson->title = $validateData['title'];
        $lesson->body = $validateData['requirements'];
        $lesson->video_provider = $videoPayload['video_provider'];
        $lesson->video_source = $videoPayload['video_source'];
        $lesson->video_id = $videoPayload['video_id'];
        $lesson->video_start_at = $videoPayload['video_start_at'];
        $lesson->video_is_preview = $videoPayload['video_is_preview'];

        if ($lesson->save()) {
            return redirect()->back()->with('success', 'You updated! '.$lesson->title);
        } else {
            return redirect()->back()->with('error', 'It isn\'t able to update the data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return redirect()->back()->with('success', 'You deleted! '.$lesson->title.' from '.$lesson->course->title);
    }
}
