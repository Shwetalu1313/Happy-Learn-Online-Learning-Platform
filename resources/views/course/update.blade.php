@extends('admin.layouts.app')
@section('content')

    @php
        use App\Enums\CourseTypeEnums;
        use App\Enums\CourseStateEnums;
        use App\Enums\UserRoleEnums;
        use App\Models\Category;
        use App\Models\SubCategory;

        $categories = Category::all();
        $subcategories = SubCategory::orderBy('name', 'asc')->get();

        $basicTypeEnumVal = CourseTypeEnums::BASIC->value;
        $advancedTypeEnumVal = CourseTypeEnums::ADVANCED->value;

        $pendingState = CourseStateEnums::PENDING->value;
        $approveState = CourseStateEnums::APPROVED->value;
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
            <div class="d-flex justify-content-between">
                <div class="card-title">Course Update Form</div>
                <button type="button" class="btn border-0 btn-outline-secondary" id="creator" data-toggle="tooltip" data-placement="left" title="{{__('course.label_creator')}}">
                    <img src="{{asset('/storage/'.$course->creator->avatar)}}" style="width: 25px; height: 25px" class="border rounded-5 border-success " alt="profile">
                    {{$course->creator->name}}
                </button>
            </div>

            <form action="{{ route('course.update',[$course->id])}}"
                  class="px-lg-5 py-lg-2 mb-5"
                  method="post"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{__('course.label_name')}}</label>
                    <input type="text"
                           class="form-control
               @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           required
                           placeholder="How to Play Guitar like a Pro"
                           value={{$course->title}}>
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
                               value="{{$basicTypeEnumVal}}"
                               @if($course->courseType == $basicTypeEnumVal) checked @endif>

                        <label class="form-check-label" for="basic">
                            {{__('course.label_t_basic')}}
                        </label>
                    </div>
                    <div class="form-check col">
                        <input class="form-check-input"
                               type="radio"
                               name="courseType"
                               id="advanced"
                               value="{{$advancedTypeEnumVal}}"
                        @if($course->courseType == $advancedTypeEnumVal) checked @endif>
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
                                   value="{{$course->fees}}"
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
                                   value="{{$course->sub_category->name}}"
                                   placeholder="somethings...." disabled>

                            <button type="button"
                                    class="btn btn-outline-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop">Choose
                            </button>
                        </div>
                        @error('sub_cate')
                        <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
            </span>
                        @enderror
                        @include('course.components.updateCategoryModel')
                    </div>
                    {{--end category--}}
                </div>
                {{--end one row--}}


                <div class="mb-3">
                    <label for="image-input" class="form-label">{{__('Image')}}</label><br>
                    <input type="file" id="image-input" class="form-control  @error('avatar') is-invalid @enderror"
                           accept="image/*" name="avatar" value="{{$course->image}}" onchange="imagePreview(event)" >
                    @error('avatar')
                    <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
        </span>
                    @enderror
                    <div id="image-preview">{{$course->image}}</div>
                    <button type="button" onclick="resetImage()"
                            class="btn btn-warning btn-sm mt-3">{{__('btnText.reset')}}</button>
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
                <button class="btn btn-primary float-end" id="btn-submit" type="submit"><i
                        class="bi bi-floppy2 me-3"></i>{{ __('btnText.update') }}</button>

            </form>
            <div class="d-flex justify-content-between px-5">
                <div>
                    <button class="btn btn-outline-primary me-3" onclick="window.location='{{url('lesson/'.$course->id.'/createForm')}}';"><i class="bi bi-file-earmark-richtext"></i> {{__('course.create_ls')}}</button>
                </div>
                <div id="approve-button-container">
                    @if($course->state === $pendingState)
                        @php
                            $isAdmin = Auth::user()->role->value === UserRoleEnums::ADMIN->value;
                        @endphp

                        @if($isAdmin && $course->creator->id != Auth::id())
                            <div>
                                <button class="btn btn-warning approve-course-btn" data-course-id="{{ $course->id }}">
                                    Approve
                                </button>
                            </div>
                        @else
                            @foreach($course->contribute_courses as $contributor)
                                @if(Auth::id() === $contributor->user->id)
                                    <div>
                                        <button class="btn btn-warning approve-course-btn" data-course-id="{{ $course->id }}">
                                            Approve
                                        </button>
                                    </div>
                                    @break <!-- Break out of the loop if contributor is found -->
                                @endif
                            @endforeach
                        @endif
                    @endif
                </div>


            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('./assets/js/imagePreviewForm.js')}}"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();


            $('.approve-course-btn').on('click', function(){
               const courseId = $(this).data('course-id');
               approveCourse(courseId);
            });

            function approveCourse(courseID){
                $.ajax({
                    url: '{{ route('course.toApprove', ["id" => ":id"]) }}'.replace(':id',courseID),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: '{{csrf_token()}}',
                        _method: 'PUT',
                    },
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                title: "Good job!",
                                text: data.message,
                                icon: "success"
                            });
                            $('.approve-course-btn').remove();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(response.message);
                    }
                });
            }
        });
    </script>
@endsection

