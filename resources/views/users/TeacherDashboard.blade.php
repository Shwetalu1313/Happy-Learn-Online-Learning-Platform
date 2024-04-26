@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3">
                <div class="card" >
                    <div class="card-body success-dark-gradient " style=" border-radius: 10px; padding: 20px;">
                        <h3 class="text-center text-light"><strong>Manage My Courses</strong></h3><br>
                        <div class="d-flex align-items-center justify-content-center coolHover" style="background-color: #e9ecef; border-radius: 8px; padding: 10px; cursor: pointer" onclick="window.location.href = '{{route('course.index')}}'">
                            <div class="d-flex align-items-center justify-content-center" style="background-color: #f8d7da; border-radius: 30%; color: #dc3545; padding: 10px; margin-right: 15px;"><i class="bi bi-journal-bookmark fs-3 text-primary"></i></div>
                            <h2 class="text-center ms-3" style="color: #0A1D56; font-size: 4rem;">{{ $courses->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row g-3 py-5">
            <div>My Course list</div>
            @foreach($courses as $course)
                <div class="col-md-4">
                    <div class="card hover-border-success" onclick="window.location='{{route('course.detail',$course->id)}}'">
                        <div class="card-header">
                            <h5>{{$course->title}}</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{asset('storage/'.$course->image)}}" class="img-fluid w-50 h-50">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
