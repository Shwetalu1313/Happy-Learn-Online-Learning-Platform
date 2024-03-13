@php
    use App\Enums\CourseTypeEnums;
    use Illuminate\Support\Facades\Route;
    $currentRoute = Route::currentRouteName();
@endphp

<form action="{{ route('course.store')}}"
      class="px-lg-5 py-lg-2"
      method="post"
      enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="mb-3">
        <label for="name" class="form-label">{{__('course.label_name')}}</label>
        <input type="text"
               class="form-control
               @error('name') is-invalid @enderror"
               id="name"
               name="name"
               required
               placeholder="How to Play Guitar like a Pro">
        @error('name')
        <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    {{--end course title--}}
    <hr>

    <div class="row mb-3">
        <small class="my-3">{{__('course.label_type')}} -></small>
        <div class="form-check col">
            <input class="form-check-input"
                   type="radio"
                   name="courseType"
                   id="basic"
                   value="{{CourseTypeEnums::BASIC->value}}">
            <label class="form-check-label" for="basic">
                {{__('course.label_t_basic')}}
            </label>
        </div>
        <div class="form-check col">
            <input class="form-check-input"
                   type="radio"
                   name="courseType"
                   id="advanced"
                   value="{{CourseTypeEnums::ADVANCED->value}}">
            <label class="form-check-label" for="advanced">
                {{__('course.label_t_advanced')}}
            </label>
        </div>
    </div>
    {{--end course type--}}
    <hr>

    <div class="row mb-3 g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">{{__('course.label_fee')}}</label>
            <div class="input-group">
                <input type="number"
                       class="form-control
                       @error('fee') is-invalid @enderror"
                       id="fee"
                       name="fee"
                       required
                       placeholder="5000"
                       min="0"
                       max="100000">
                <span class="input-group-text">{{__('course.label_kyat')}}</span>
            </div>
            @error('fee')
            <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
            @enderror
            <small class="text-warning">*** {{__('course.label_type_announcement')}}</small>
        </div>
        {{--end fee --}}

        <div class="col-md-6">
            <label for="name" class="form-label">{{__('course.label_ask_cate')}}</label>
            <div class="input-group">
                <input type="text"
                       class="form-control
                       @error('sub_cate') is-invalid @enderror"
                       id="sub_cate"
                       name="sub_cate"
                       required
                       placeholder="somethings...." disabled>

                <button type="button"
                        class="btn btn-outline-info"
                        data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop">Choose</button>
            </div>
            @error('sub_cate')
            <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
            </span>
            @enderror
            @include('course.components.categoryModel')
        </div>
        {{--end category--}}
    </div>
    {{--end one row--}}


    <div class="mb-3">
        <label for="avatar" class="form-label">{{__('Image')}}</label>
        <input type="file" id="avatar" class="form-control  @error('avatar') is-invalid @enderror" accept="image/*" name="avatar" onchange="imagePreview(event)">
        @error('avatar')
        <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
        </span>
        @enderror
        <div id="image-preview"></div>
        <button type="button" onclick="resetImage()" class="btn btn-warning btn-sm mt-3">{{__('btnText.reset')}}</button>
    </div>
    {{--end Image--}}
    <hr>

    <div class="mb-3">
        @error('requirements')
        <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
        </span>
        @enderror
        <small class="mb-3">Descriptions</small>
        <div class="quill-editor-full" id="req">
            <p>Hello world</p>
        </div>
        <input type="hidden" name="requirements" id="req_input">
    </div>
    {{--end text editor for description--}}
    <hr>
    <button class="btn btn-primary float-end mt-3" id="btn-submit"><i class="bi bi-floppy2 me-3"></i>{{ __('btnText.save') }}</button>

</form>
