@extends('admin.layouts.app')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2-circle text-success"></i> {{session('success')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <form method="POST" action="{{route('lesson.store')}}" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <div class="card">
            <div class="card-body">
                    <h5 class="card-title border border-2 px-3 mt-3 rounded" contenteditable="true" id="title">Lesson Title Editable</h5>
                <label for="course" class="form-label">{{__('course.list_title')}}</label>
                <select class="form-select mb-5 border-black" aria-label="Choose Course" id="course" name="course">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                                @if($course->id == $course_id)
                                    selected
                            @endif>
                            {{ $loop->iteration }}. {{ $course->title }}
                        </option>
                    @endforeach

                </select>
                @error('sub_cate_select')
                <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                @enderror
                <div class="quill-editor-full" id="req">
                    <p>Hello world</p>
                </div>
                <input type="hidden" name="title" id="title_input">
                <input type="hidden" name="requirements" id="req_input">
                <button class="btn btn-primary float-end mt-3" type="submit" id="btn-submit">{{ __('btnText.save') }}</button>
            </div>
        </div>
    </form>
@endsection
