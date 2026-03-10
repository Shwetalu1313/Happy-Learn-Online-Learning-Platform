@extends('admin.layouts.app')

@section('content')
    @php
        $hasBetaCourse = $courses->contains(fn ($course) => !isset($course->approvedUser_id));
     @endphp
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-0">{{__('lesson.list_title')}}</h5>
                <small class="text-muted">Open a course to create, review, and manage lessons.</small>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('course.index') }}">
                <i class="bi bi-journal-bookmark me-1"></i>Open Course List
            </a>
        </div>
        <div class="card p-3">
            <div class="card-body">
                @if($hasBetaCourse)
                    <small class="mb-3">Beta version are the courses that are awaiting approval.</small>
                @endif

                @if($courses->isEmpty())
                    <div class="alert alert-secondary mb-0">
                        No courses available yet for your account. Create or request course access first.
                    </div>
                @endif

                <div class="row g-3">
                    @foreach($courses as $course)
                        @php
                            $newBadgeDays = 7;
                            $isNew = now()->diffInDays($course->created_at) <= $newBadgeDays;
                            $isBeta = !isset($course->approvedUser_id);
                        @endphp
                        <div class="col-md-6">
                            <div class="accordion" id="accordionPanelsStayOpenExample{{$loop->iteration}}">
                                <div class="accordion-item border-black border-opacity-25">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button softGradient" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse{{$loop->iteration}}" aria-expanded="true" aria-controls="panelsStayOpen-collapse{{$loop->iteration}}">
                                            <img src="{{ asset('/storage/'.$course->image) }}" style="width: 25px; height: 25px" class="border rounded-5 border-success me-3" alt="profile">
                                            {{ $course->title }}
                                            @if($isNew)
                                                <span class="ms-3  badge text-bg-info">new</span>
                                            @endif
                                            @if($isBeta)
                                                <span class="ms-1 badge text-bg-warning">beta</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapse{{$loop->iteration}}" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <a href="{{ url('course/'.$course->id.'/edit') }}">Click here to edit {{ $course->title }}.</a>
                                            <hr>
                                            @if($course->lessons->count() === 0)
                                                <div class="text-muted mb-2">No lesson found.</div>
                                            @else
                                                @foreach($course->lessons as $lesson)
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a href="{{url('lesson/'.$lesson->id.'/review')}}" class="text-black">{{$loop->iteration}}. {{$lesson->title}}</a>
                                                        <button class="btn btn-danger btn-sm" onclick="document.getElementById('lesson{{$lesson->id}}-destroy').submit()"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                    <form action="{{route('lesson.destroy', $lesson->id)}}" class="d-none" method="post" id="lesson{{$lesson->id}}-destroy">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endforeach
                                            @endif
                                            <hr>
                                            <button class="btn btn-outline-primary" onclick="window.location='{{url('lesson/'.$course->id.'/createForm')}}';"><i class="bi bi-file-earmark-richtext"></i> {{__('course.create_ls')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
