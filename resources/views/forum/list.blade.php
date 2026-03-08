@extends('layouts.app')

@section('content')
    <style>
        .forum-shell {
            --forum-bg: #0b1220;
            --forum-card: #121a2b;
            --forum-line: #263551;
            --forum-text: #e8eef9;
            --forum-muted: #95a7c5;
            --forum-accent: #4f8cff;
            --forum-accent-soft: #1a2842;
            --forum-success: #34d399;
            --forum-danger: #f87171;
            color: var(--forum-text);
            background: linear-gradient(180deg, rgba(9, 14, 25, 0.86), rgba(8, 13, 22, 0.95));
            border: 1px solid #1d2941;
            border-radius: 18px;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.35);
        }

        .forum-hero {
            border: 1px solid var(--forum-line);
            border-radius: 18px;
            background: linear-gradient(130deg, #17243b 0%, #132137 58%, #101b2e 100%);
            padding: 1rem;
            margin-bottom: 0.9rem;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.3);
        }

        .forum-hero h4 {
            margin: 0;
            font-weight: 800;
            color: var(--forum-text);
        }

        .forum-hero .breadcrumb-item,
        .forum-hero .breadcrumb-item.active {
            color: var(--forum-muted);
        }

        .forum-hero .breadcrumb-item a {
            color: #9ec2ff;
            text-decoration: none;
        }

        .forum-hero .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .forum-hero p {
            margin: 0.35rem 0 0;
            color: var(--forum-muted);
        }

        .forum-grid {
            display: grid;
            grid-template-columns: minmax(260px, 330px) 1fr;
            gap: 0.9rem;
        }

        .forum-panel,
        .forum-feed-card {
            background: var(--forum-card);
            border: 1px solid var(--forum-line);
            border-radius: 14px;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.25);
        }

        .forum-feed-card:hover {
            border-color: #36507a;
        }

        .forum-panel {
            padding: 0.95rem;
            height: fit-content;
            position: sticky;
            top: 92px;
        }

        .forum-panel h6 {
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .forum-panel p {
            font-size: 0.86rem;
            color: var(--forum-muted);
        }

        .forum-panel textarea,
        .forum-panel input,
        .forum-comment-form textarea,
        .forum-search {
            border: 1px solid #35507a;
            background: #0e1627;
            color: var(--forum-text);
            border-radius: 10px;
            font-size: 0.92rem;
        }

        .forum-panel textarea::placeholder,
        .forum-comment-form textarea::placeholder,
        .forum-search::placeholder {
            color: #7f93b1;
        }

        .forum-search {
            margin-bottom: 0.75rem;
        }

        .forum-feed {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .forum-feed-card {
            overflow: hidden;
        }

        .forum-feed-body {
            padding: 0.92rem;
        }

        .forum-head {
            display: flex;
            justify-content: space-between;
            gap: 0.6rem;
            align-items: flex-start;
            margin-bottom: 0.55rem;
        }

        .forum-author {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
        }

        .forum-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid #3a4f72;
            background: #152338;
        }

        .forum-author h6 {
            margin: 0;
            font-size: 0.94rem;
            font-weight: 800;
            color: var(--forum-text);
        }

        .forum-author a {
            color: var(--forum-text);
        }

        .forum-author a:hover {
            color: #9ec2ff;
        }

        .forum-author small {
            color: var(--forum-muted);
        }

        .forum-text {
            margin: 0;
            color: #d3ddf1;
            line-height: 1.56;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .forum-actions {
            margin-top: 0.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            align-items: center;
        }

        .forum-actions .btn {
            border-radius: 9px;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.32rem 0.62rem;
        }

        .forum-actions .btn-outline-secondary {
            border-color: #466089;
            color: #bfd0eb;
        }

        .forum-actions .btn-outline-secondary:hover {
            background: #223557;
            border-color: #5f7eaf;
            color: #e6eef9;
        }

        .forum-comment-list {
            margin-top: 0.8rem;
            border-top: 1px solid #2d4061;
            padding-top: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .forum-comment-item {
            border: 1px solid #2e4263;
            border-radius: 10px;
            padding: 0.58rem;
            background: #0f1828;
        }

        .forum-reply-list {
            margin-top: 0.65rem;
            margin-left: 1.1rem;
            border-left: 2px solid #2b4266;
            padding-left: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .forum-reply-item {
            border: 1px solid #2f4b73;
            border-radius: 10px;
            padding: 0.52rem;
            background: #111e31;
        }

        .forum-comment-head {
            display: flex;
            justify-content: space-between;
            gap: 0.55rem;
            margin-bottom: 0.3rem;
            align-items: center;
        }

        .forum-comment-author {
            color: #9ec2ff;
            font-weight: 700;
            font-size: 0.84rem;
            text-decoration: none;
        }

        .forum-comment-author:hover {
            text-decoration: underline;
        }

        .forum-comment-text {
            margin: 0;
            color: #c8d7ee;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 0.9rem;
        }

        .forum-feed-body .text-muted,
        .forum-comment-item .text-muted {
            color: var(--forum-muted) !important;
        }

        .forum-comment-form {
            margin-top: 0.7rem;
            border-top: 1px dashed #34527c;
            padding-top: 0.7rem;
        }

        .forum-reply-form {
            margin-top: 0.55rem;
            border-top: 1px dashed #385a88;
            padding-top: 0.55rem;
        }

        .forum-comment-item .btn-link,
        .forum-reply-item .btn-link {
            color: #9ec2ff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0;
        }

        .forum-comment-item .btn-link:hover,
        .forum-reply-item .btn-link:hover {
            color: #c8dcff;
            text-decoration: underline;
        }

        .forum-empty {
            padding: 1.2rem;
            text-align: center;
            color: var(--forum-muted);
            border: 1px dashed #34527c;
            border-radius: 14px;
            background: #0f1a2b;
        }

        @media (max-width: 991px) {
            .forum-grid {
                grid-template-columns: 1fr;
            }

            .forum-panel {
                position: static;
            }
        }
    </style>

    <div class="container py-4 forum-shell">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-bag-x me-2"></i>{{ Session::pull('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2-circle me-2"></i>{{ Session::pull('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="forum-hero">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('course.detail', $lesson->course_id) }}">{{ $lesson->course->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $lesson->title }}</li>
                    <li class="breadcrumb-item active" aria-current="page">Discussion</li>
                </ol>
            </nav>
            <h4>Lesson Discussion Board</h4>
            <p>Ask, answer, and discuss in one clean thread view. Start a post on the left, then manage comments inline.</p>
        </section>

        <div class="forum-grid">
            <aside class="forum-panel">
                <h6>Create a New Post</h6>
                <p>Keep your post short and clear so others can respond quickly.</p>
                <form action="{{ route('forums.store') }}" method="POST" class="d-grid gap-2">
                    @csrf
                    <input type="hidden" value="{{ $lesson->id }}" name="lesson_id">
                    <textarea class="form-control" id="forum_text" name="text" rows="5" maxlength="255" placeholder="What do you want to discuss?" required>{{ old('text') }}</textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Post Discussion
                    </button>
                </form>
            </aside>

            <section>
                <input type="text" id="forumSearch" class="form-control forum-search" placeholder="Search in this lesson discussion...">

                @if($lesson->forums->isEmpty())
                    <div class="forum-empty">
                        <h6 class="mb-1">No posts yet</h6>
                        <p class="mb-0">Be the first to start the discussion for this lesson.</p>
                    </div>
                @else
                    <div class="forum-feed" id="forumFeed">
                        @foreach($lesson->forums as $forum)
                            <article class="forum-feed-card forum-post-item" data-search="{{ strtolower($forum->text.' '.$forum->user->name) }}">
                                <div class="forum-feed-body">
                                    <div class="forum-head">
                                        <div class="forum-author">
                                            <img src="{{ asset('storage/'.ltrim($forum->user->avatar, '/')) }}" class="forum-avatar" alt="profile">
                                            <div>
                                                <h6>
                                                    <a class="text-decoration-none" href="{{ url('profile/'. $forum->user->id) }}">{{ $forum->user->name }}</a>
                                                </h6>
                                                <small>{{ $forum->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @if($forum->user_id == auth()->id())
                                            <form action="{{ route('forums.destroy', $forum) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endif
                                    </div>

                                    <p class="forum-text">{{ $forum->text }}</p>

                                    <div class="forum-actions">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#commentsPanel{{ $forum->id }}" aria-expanded="false" aria-controls="commentsPanel{{ $forum->id }}">
                                            {{ $forum->comments_count }} Comment(s)
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addCommentPanel{{ $forum->id }}" aria-expanded="false" aria-controls="addCommentPanel{{ $forum->id }}">
                                            Add Comment
                                        </button>
                                    </div>

                                    <div class="collapse" id="commentsPanel{{ $forum->id }}">
                                        <div class="forum-comment-list">
                                            @forelse($forum->rootComments as $comment)
                                                <div class="forum-comment-item">
                                                    <div class="forum-comment-head">
                                                        <a class="forum-comment-author" href="{{ url('profile/'. $comment->user->id) }}">
                                                            {{ $comment->user->name }}
                                                            @if($comment->user->id === $forum->user->id)
                                                                <span class="text-muted">(author)</span>
                                                            @endif
                                                        </a>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                            @if($comment->user_id == auth()->id())
                                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="forum-comment-text">{{ $comment->text }}</p>

                                                    <button class="btn btn-link btn-sm mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#replyCommentPanel{{ $comment->id }}" aria-expanded="false" aria-controls="replyCommentPanel{{ $comment->id }}">
                                                        Reply
                                                    </button>

                                                    <div class="collapse" id="replyCommentPanel{{ $comment->id }}">
                                                        <div class="forum-reply-form">
                                                            <form action="{{ route('comments.store') }}" method="POST" class="d-grid gap-2">
                                                                @csrf
                                                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                                <textarea class="form-control" name="text" rows="2" maxlength="255" placeholder="Write your reply..." required></textarea>
                                                                <button type="submit" class="btn btn-primary btn-sm">Submit Reply</button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    @if($comment->replies->isNotEmpty())
                                                        <div class="forum-reply-list">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="forum-reply-item">
                                                                    <div class="forum-comment-head">
                                                                        <a class="forum-comment-author" href="{{ url('profile/'. $reply->user->id) }}">
                                                                            {{ $reply->user->name }}
                                                                            @if($reply->user->id === $forum->user->id)
                                                                                <span class="text-muted">(author)</span>
                                                                            @endif
                                                                        </a>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                                            @if($reply->user_id == auth()->id())
                                                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" onsubmit="return confirm('Delete this reply?')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <p class="forum-comment-text">{{ $reply->text }}</p>

                                                                    <button class="btn btn-link btn-sm mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#replyCommentPanel{{ $reply->id }}" aria-expanded="false" aria-controls="replyCommentPanel{{ $reply->id }}">
                                                                        Reply
                                                                    </button>

                                                                    <div class="collapse" id="replyCommentPanel{{ $reply->id }}">
                                                                        <div class="forum-reply-form">
                                                                            <form action="{{ route('comments.store') }}" method="POST" class="d-grid gap-2">
                                                                                @csrf
                                                                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                                                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                                                <textarea class="form-control" name="text" rows="2" maxlength="255" placeholder="Write your reply..." required></textarea>
                                                                                <button type="submit" class="btn btn-primary btn-sm">Submit Reply</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="text-muted">No comments yet.</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="collapse" id="addCommentPanel{{ $forum->id }}">
                                        <div class="forum-comment-form">
                                            <form action="{{ route('comments.store') }}" method="POST" class="d-grid gap-2">
                                                @csrf
                                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                <textarea class="form-control" name="text" rows="3" maxlength="255" placeholder="Write your comment..." required></textarea>
                                                <button type="submit" class="btn btn-primary btn-sm">Submit Comment</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('forumSearch');
            const postItems = document.querySelectorAll('.forum-post-item');

            if (!searchInput || postItems.length === 0) {
                return;
            }

            searchInput.addEventListener('input', function () {
                const term = (this.value || '').toLowerCase().trim();

                postItems.forEach((item) => {
                    const target = item.getAttribute('data-search') || '';
                    item.style.display = target.includes(term) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
