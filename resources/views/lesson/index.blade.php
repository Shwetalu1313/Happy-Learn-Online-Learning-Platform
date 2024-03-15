@extends('admin.layouts.app')

@section('content')
    @php
        $betaFound = true;
     @endphp
    <div class="container">
        <div class="card p-3">
            <div class="card-title">{{__('lesson.list_title')}}</div>
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
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-bag-x me-3"></i> {{session('error')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check2-circle text-success me-3"></i> {{session('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{--end alert--}}
            <div class="card-body">
                @if($betaFound)
                    <small class="mb-3">Beta version are the courses that are awaiting approval.</small>
                @endif
                <div class="row g-3">
                    @foreach($courses as $course)
                        @php
                            $newBadgeDays = 7;
                            $isNew = now()->diffInDays($course->created_at) <= $newBadgeDays;
                            $isBeta = !isset($course->approvedUser_id);
                            $betaFound = $isBeta;
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
                                                No Lesson Found!
                                            @else
                                                @foreach($course->lessons as $lesson)
                                                    <a href="#" class="text-black mb-3">{{$loop->iteration}}. {{$lesson->title}}</a><br>
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
