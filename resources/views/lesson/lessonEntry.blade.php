@extends('admin.layouts.app')
@section('content')
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Create Lesson</h5>
        <a href="{{ route('lesson.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back To Lesson List
        </a>
    </div>
    <form method="POST" action="{{route('lesson.store')}}" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border border-2 px-3 mt-3 rounded" contenteditable="true" id="title">{{ old('title', 'Lesson Title Editable') }}</h5>
                <label for="course" class="form-label">{{__('course.list_title')}}</label>
                <select class="form-select mb-5 border-black" aria-label="Choose Course" id="course" name="course">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                                @if((int) old('course', $course_id) === (int) $course->id)
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

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="video_provider" class="form-label">Video Provider</label>
                        <select id="video_provider" name="video_provider" class="form-select border-black">
                            <option value="">No video</option>
                            @foreach($videoProviders as $videoProvider)
                                <option value="{{ $videoProvider }}" {{ old('video_provider') === $videoProvider ? 'selected' : '' }}>
                                    {{ config('lesson_video.providers.' . $videoProvider . '.label', ucfirst($videoProvider)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="video_source" class="form-label">YouTube URL or Video ID</label>
                        <input
                            type="text"
                            name="video_source"
                            id="video_source"
                            class="form-control border-black"
                            value="{{ old('video_source') }}"
                            placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX or XXXXXXXXXXX"
                        >
                        <small class="text-secondary">Only YouTube is enabled for now. Architecture is provider-ready for future upgrades.</small>
                    </div>
                    <div class="col-md-3">
                        <label for="video_start_at" class="form-label">Start At (seconds)</label>
                        <input
                            type="number"
                            min="0"
                            max="86400"
                            name="video_start_at"
                            id="video_start_at"
                            class="form-control border-black"
                            value="{{ old('video_start_at', 0) }}"
                        >
                        <div class="form-check mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="video_is_preview"
                                name="video_is_preview"
                                {{ old('video_is_preview') ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="video_is_preview">
                                Mark as preview lesson
                            </label>
                        </div>
                    </div>
                </div>

                <div class="quill-editor-full" id="req">
                    <p>{{ old('requirements', 'Lesson content goes here...') }}</p>
                </div>
                <input type="hidden" name="title" id="title_input">
                <input type="hidden" name="requirements" id="req_input">
                <button class="btn btn-primary float-end mt-3" type="submit" id="btn-submit">{{ __('btnText.save') }}</button>
            </div>
        </div>
    </form>
@endsection
