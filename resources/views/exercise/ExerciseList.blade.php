@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <button class="btn btn-secondary mb-3" onclick="window.location='{{route('course.detail',$lesson->course_id)}}'">Back</button>

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

        @foreach($lesson->exercises as $exercise)
            @if($exercise->questions->count() > 0)
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="text-secondary">{{$exercise->title}}</h4>
                        <p class="">{{$exercise->content}}</p>
                        <div class="text-end">
                            <small class="text-secondary-emphasis">Total Questions :: {{$exercise->questions->count()}}</small>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary" onclick="window.location='{{route('exercise.questions_learner_form',$exercise)}}'">Start</button>
                        {{--//This is route to question form--}}
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
