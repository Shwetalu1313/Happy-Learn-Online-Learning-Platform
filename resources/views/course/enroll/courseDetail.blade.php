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

        <div class="d-flex flex-row align-items-baseline">
            <button class="btn btn-outline-primary mb-5 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLesson" aria-controls="offcanvasLesson">
                <i class="bi bi-file-earmark-image"></i> {{__('lesson.title')}}
            </button>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLesson" aria-labelledby="Lesson List">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-info-emphasis" id="offcanvasLessonlebel">{{__('lesson.title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    @foreach($course->lessons as $lesson)
                        <button class="nav-link @if($loop->first) active @endif" id="lesson-{{ $lesson->id }}-tab" data-bs-toggle="pill" data-bs-target="#lesson-{{ $lesson->id }}" type="button" role="tab" aria-controls="lesson-{{ $lesson->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $lesson->title }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Body --}}
        <h3 class="text-info-emphasis text-center">{{$course->title}}</h3>

        {{-- Exercise List Button --}}
{{--            <button class="btn btn-secondary mb-3 btn-redirect-exercise" data-lesson-id="{{ $lesson->id }}">{{__('exercise.pg_title')}}</button>--}}

        <div class="tab-content" id="v-pills-tabContent">
            @foreach($course->lessons as $lesson)
                <div class="tab-pane fade @if($loop->first) show active @endif" id="lesson-{{ $lesson->id }}" role="tabpanel" aria-labelledby="lesson-{{ $lesson->id }}-tab" tabindex="0">
                    <div class="card">
                        {{-- Exercise List Button --}}
                        <div>
                            <button class="btn btn-secondary mb-3 m-3 me-3" onclick="window.location='{{url('exercise/list/'.$lesson->id)}}' ">{{__('exercise.pg_title')}}</button>
                            <button class="btn btn-secondary mb-3 m-3" onclick="window.location='{{route('forums',$lesson)}}' ">{{__('Discussion')}}</button>
                        </div>


                        <div class="card-body">
                            <h5 class="card-title">{{ $lesson->title }}</h5>
                            <div class="ql-editor lesson-body-{{ $lesson->id }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Function to decode HTML entities
        function decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        $(document).ready(function () {

            // Populate lesson bodies
            @foreach($course->lessons as $lesson)
            $('.lesson-body-{{ $lesson->id }}').html(decodeHtml("{{ $lesson->body }}"));
            @endforeach
        });

    </script>
@endsection
