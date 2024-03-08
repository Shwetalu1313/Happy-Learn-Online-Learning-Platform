@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="card-title">{{__('cate.cate_ent')}}</div>
                <form action="{{ route('category.store') }}" class="px-lg-5 py-lg-2" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('cate.cate_name')}}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">{{__('cate.cate_img')}}</label>
                        <input type="file" id="avatar" class="form-control  @error('avatar') is-invalid @enderror" accept="image/*" name="avatar" onchange="imagePreview(event)">
                        @error('avatar')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div id="image-preview"></div>
                        <button type="button" onclick="resetImage()" class="btn btn-warning btn-sm mt-3">{{__('btnText.reset')}}</button>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary float-end">{{__('btnText.save')}}</button>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('./assets/js/imagePreviewForm.js')}}"></script>
@endsection
