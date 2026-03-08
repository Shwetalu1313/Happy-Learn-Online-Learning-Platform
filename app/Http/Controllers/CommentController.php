<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Forum;
use App\Models\User;
use App\Services\NotificationManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function store(Request $request, NotificationManager $notificationManager)
    {
        $request->validate([
            'text' => 'required|string|max:255', // Adjust the max length as needed
            'forum_id' => 'required|exists:forums,id',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(
                    fn ($query) => $query->where('forum_id', $request->forum_id)
                ),
            ],
        ]);

        $actor = auth()->user();
        $parentId = null;
        $targetCommentOwnerId = null;

        if (! empty($request->parent_id)) {
            $parentId = $request->parent_id;
            $parentComment = Comment::query()
                ->select(['id', 'forum_id', 'parent_id', 'user_id'])
                ->where('id', $parentId)
                ->where('forum_id', $request->forum_id)
                ->first();

            if ($parentComment) {
                $targetCommentOwnerId = $parentComment->user_id;
                // Keep one visible thread level in UI by grouping replies under the root comment.
                $parentId = $parentComment->parent_id ?: $parentComment->id;
            }
        }

        $newComment = Comment::create([
            'text' => $request->text,
            'user_id' => $actor->id,
            'forum_id' => $request->forum_id,
            'parent_id' => $parentId,
        ]);

        $forum = Forum::query()
            ->with('user:id,name,email')
            ->select(['id', 'lesson_id', 'text', 'user_id'])
            ->find($request->forum_id);

        if ($forum && $forum->user && $forum->user->id !== $actor->id) {
            if ($targetCommentOwnerId !== $forum->user->id || is_null($targetCommentOwnerId)) {
                $notificationManager->notifyForumNewComment($forum->user, $actor, $forum, $newComment);
            }
        }

        if (! is_null($targetCommentOwnerId) && $targetCommentOwnerId !== $actor->id) {
            $targetUser = User::query()->find($targetCommentOwnerId);
            if ($targetUser && $forum) {
                $notificationManager->notifyForumReply($targetUser, $actor, $forum, $newComment);
            }
        }

        return redirect()->back()->with('success', 'Comment created successfully.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id == auth()->id()) {
            $comment->replies()->each(function (Comment $reply) {
                $reply->delete();
            });
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }
    }
}
