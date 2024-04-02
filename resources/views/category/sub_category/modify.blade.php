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
                <div class="card-title">{{__('cate.sub_cate_ent')}}</div>
                <form action="{{ route('sub_category.update', $sub_category) }}" class="px-lg-5 py-lg-2" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('cate.sub_cate_name')}}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{$sub_category->name}}" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">{{__('cate.sub_cate_img')}}</label>
                        <input type="file" id="avatar" class="form-control  @error('avatar') is-invalid @enderror" accept="image/*" value="{{$sub_category->img_path}}" name="avatar" onchange="imagePreview(event)">
                        @error('avatar')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div id="image-preview"></div>
                        <button type="button" onclick="resetImage()" class="btn btn-warning btn-sm mt-3">{{__('btnText.reset')}}</button>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">{{__('cate.cate')}}</label>
                        <select class="form-select" aria-label="Choose Sub-Category" id="category" name="category_id">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @if($category->name === $sub_category->category->name) selected @endif>{{ $loop->iteration }}. {{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('select')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary float-end">{{__('btnText.save')}}</button>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function imagePreview(event) {
            event.preventDefault();
            const preview = document.getElementById('image-preview');
            const file = event.target.files[0];
            const reader = new FileReader();
            preview.style.maxWidth = '150px';
            preview.style.maxHeight = '150px';
            preview.style.marginTop = '20px';

            reader.onload = () => {
                const img = new Image();
                img.src = reader.result;
                img.style.maxWidth = "100%";
                img.style.maxHeight = "100%";

                preview.innerHTML = '';
                preview.appendChild(img);
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function resetImage() {
            const input = document.getElementById('avatar');
            input.value = '';
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
        }


    </script>
@endsection
