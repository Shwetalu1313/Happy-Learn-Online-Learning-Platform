@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h3>My Library</h3>
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
        <div class="row g-3 ">
            @foreach(auth()->user()->enrollCourses as $enrollCourse)
                <div class="col-md-4">
                    <div class="card hover-border-success" onclick="window.location='{{route('course.detail',$enrollCourse->course_id)}}'">
                        <div class="card-header">
                            <h5>{{$enrollCourse->course->title}}</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{asset('storage/'.$enrollCourse->course->image)}}" class="img-fluid w-50 h-50">
                        </div>
                        <div class="card-footer d-flex flex-row-reverse">
                            <form action="{{ route('enroll.delete', $enrollCourse) }}" method="post" id="course_{{ $enrollCourse->id }}_delete">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-danger" onclick="return confirm('Do you really want to unsubscribe from this course: {{ $enrollCourse->course->title }}?')">Unsubscribe <i class="bi bi-ban"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
