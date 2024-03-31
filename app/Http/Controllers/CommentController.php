<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:255', // Adjust the max length as needed
            'forum_id' => 'required|exists:forums,id',
        ]);

        Comment::create([
            'text' => $request->text,
            'user_id' => auth()->id(),
            'forum_id' => $request->forum_id,
        ]);

        return redirect()->back()->with('success', 'Comment created successfully.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id == auth()->id()) {
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }
    }
}
