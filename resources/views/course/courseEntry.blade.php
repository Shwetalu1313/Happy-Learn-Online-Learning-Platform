@extends('admin.layouts.app')
@section('content')

    @php
        use App\Models\Category;
        use App\Models\SubCategory;

        $categories = Category::all();
        $subcategories = SubCategory::orderBy('name', 'asc')->get();

    @endphp
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
    <div class="card">
        <div class="card-body">
            <div class="card-title">Course Entry Form</div>
            @include('course.components.postForm')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('./assets/js/imagePreviewForm.js')}}"></script>
@endsection
