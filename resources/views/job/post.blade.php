@extends('admin.layouts.app')
@section('content')

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form method="POST" action="{{ url('/job/store') }}">
        @csrf

        <div class="card">
            <div class="card-body">
                <h5 class="card-title" contenteditable="true" id="title">Job Title Editable</h5>
                <div class="quill-editor-full" id="req">
                    <p>Hello world</p>
                </div>
                <input type="hidden" name="title" id="title_input">
                <input type="hidden" name="requirements" id="req_input">
                <button class="btn btn-primary float-end mt-3" id="btn-submit">{{ __('btnText.save') }}</button>
            </div>
        </div>
    </form>

@endsection
