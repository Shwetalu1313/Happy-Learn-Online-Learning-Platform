<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function showForumList(Lesson $lesson){
        $titlePage = $lesson->title;
        $lesson->load([
            'course:id,title',
            'forums' => function ($query) {
                $query->with([
                    'user:id,name,avatar',
                    'rootComments' => fn ($commentQuery) => $commentQuery
                        ->with([
                            'user:id,name,avatar',
                            'replies.user:id,name,avatar',
                        ])
                        ->oldest(),
                ])
                ->withCount('comments')
                ->latest();
            },
        ]);

        return view('forum.list', compact('titlePage', 'lesson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:255', // Adjust the max length as needed
        ]);

        Forum::create([
            'text' => $request->text,
            'user_id' => auth()->id(),
            'lesson_id' => $request->lesson_id,
        ]);

        return redirect()->back()->with('success', 'Forum created successfully.');
    }

    public function destroy(Forum $forum)
    {
        if ($forum->user_id == auth()->id()) {
            $forum->comments()->delete(); // Assuming you have a comments relationship defined in the Forum model
            $forum->delete();
            return redirect()->back()->with('success', 'Forum deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You are not authorized to delete this forum.');
        }
    }
}
