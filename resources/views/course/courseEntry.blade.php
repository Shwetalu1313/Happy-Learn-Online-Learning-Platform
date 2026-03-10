@extends('admin.layouts.app')
@section('content')

    @php
        use App\Models\Category;
        use App\Models\SubCategory;

        $categories = Category::all();
        $subcategories = SubCategory::orderBy('name', 'asc')->get();

    @endphp

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Create Course</h5>
        <a href="{{ route('course.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back To Course List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">Course Entry Form</div>
            @include('course.components.postForm')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('./assets/js/imagePreviewForm.js')}}"></script>
    @if(old('requirements'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editor = document.querySelector('#req .ql-editor');
                if (editor) {
                    editor.innerHTML = @json(old('requirements'));
                }
            });
        </script>
    @endif
@endsection
