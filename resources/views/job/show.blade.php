@extends('admin.layouts.app')
@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="rec-h d-none">
     d
    </div>


    <form method="POST" action="{{route('job.update', $job->id)}}" >
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" contenteditable="true" id="title">{{ $job->title }}</h5>
                <div class="quill-editor-full" id="req"></div>
                <input type="hidden" name="title" id="title_input">
                <input type="hidden" name="requirements" id="req_input">
                <button class="btn btn-primary float-end mt-3" id="btn-submit">{{ __('btnText.update') }}</button>
            </div>
        </div>
    </form>

@endsection
