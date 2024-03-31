@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h3>My Library</h3>
        <div class="row g-3 ">
            @foreach(auth()->user()->enrollCourses as $enrollCourse)
                <div class="col-md-4">
                    <div class="card hover-border-success" onclick="window.location='{{route('course.detail',$enrollCourse->course_id)}}'">
                        <div class="card-body">
                            <h3>{{$enrollCourse->course->title}}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
