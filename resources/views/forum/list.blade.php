@extends('layouts.app')

@section('content')
    <div class="container py-5">

        {{--alert--}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{$error}}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-bag-x me-3"></i> {{ Session::pull('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2-circle text-success me-3"></i> {{ Session::pull('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{--end alert--}}

        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('course.detail', $lesson->course_id)}}">{{$lesson->course->title}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$lesson->title}}</li>
                <li class="breadcrumb-item active" aria-current="page">Discussion</li>
            </ol>
        </nav>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createForumModal">
            Create a Post
        </button>

        <div class="modal fade" id="createForumModal" tabindex="-1" aria-labelledby="createForumModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create Forum</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('forums.store') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group mb-3">
                                <label for="forum_text">Forum Text</label>
                                <textarea class="form-control" id="forum_text" name="text" rows="3" required></textarea>
                                <input type="hidden" value="{{$lesson->id}}" name="lesson_id">
                            </div>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            @foreach($lesson->forums as $forum)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body bg--first">
                            <h5 class="card-title" id="shortText_{{$forum->id}}">{{ Str::limit($forum->text, $limit = 50, $end = '...') }}</h5>
                            @if(strlen($forum->text) > 50)
                                <a href="#" onclick="showFullText('{{ $forum->id }}')" id="see_more_{{$forum->id}}">See more ...</a> <br>
                                <h5 id="fullText_{{ $forum->id }}" style="display: none;" class="my-3">{{ $forum->text }}</h5>
                                <br>
                            @endif
                            @if($forum->user_id == auth()->id())
                                <form action="{{ route('forums.destroy', $forum) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            @endif

                            <!-- Create Comment Button -->
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createCommentModal{{ $forum->id }}">
                                Create Comment
                            </button>

                            <!-- Modal for Showing Comments -->
                            <div class="modal fade" id="showCommentModal{{ $forum->id }}" tabindex="-1" role="dialog" aria-labelledby="showCommentModalLabel{{ $forum->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="showCommentModalLabel{{ $forum->id }}">"{{ $forum->text }}"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @foreach($forum->comments as $comment)
                                                <a class="text-secondary-emphasis d-block" href="{{ url('profile/'. $comment->user->id) }}">{{$comment->user->name}} @if($comment->user->id === $forum->user->id)(author) @endif</a>
                                                <p class="fs-5">{{$comment->text}}</p>
                                                @if($comment->user_id == auth()->id())
                                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                @endif
                                                <hr>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for Creating Comment -->
                            <div class="modal fade" id="createCommentModal{{ $forum->id }}" tabindex="-1" aria-labelledby="createCommentModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="createCommentModalLabel">Create Comment</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('comments.store') }}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                <div class="form-group mb-3">
                                                    <label for="comment_text">Comment</label>
                                                    <textarea class="form-control" id="comment_text" name="text" rows="3" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer text-end d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{asset('/storage/'.$forum->user->avatar)}}" style="width: 25px; height: 25px" class="border rounded-5 border-success me-1" alt="profile">
                                <a class="text-secondary-emphasis" href="{{ url('profile/'. $forum->user->id) }}">{{$forum->user->name}}</a>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#showCommentModal{{ $forum->id }}">
                                {{$forum->comments->count()}} comments <i class="bi bi-chat-dots mx-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function showFullText(forumId) {
            const fullTextElement = document.getElementById('fullText_' + forumId);
            const shortText = document.getElementById('shortText_'+forumId);
            const seeMoreText = document.getElementById('see_more_'+forumId);
            if (fullTextElement.style.display === "none") {
                fullTextElement.style.display = "block";
                shortText.classList.add('d-none');
                seeMoreText.innerText = "see less ...";
            } else {
                fullTextElement.style.display = "none";
                shortText.classList.remove('d-none');
                seeMoreText.innerText = "see more ..."
            }
        }
    </script>

@endsection
