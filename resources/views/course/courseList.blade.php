@extends('admin.layouts.app')
@section('content')
    @php
        use App\Enums\UserRoleEnums;
        use App\Enums\CourseStateEnums;
        use App\Enums\CourseTypeEnums;

        if (!function_exists('isCreatorOfAdmin')) {
            function isCreatorOfAdmin($course){
                return auth()->id() === $course->createdUser_id || auth()->user()->role->value === UserRoleEnums::ADMIN->value;
            }
        }


    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="mb-0">Course Management</h5>
                    <small class="text-muted">Manage courses, lesson access, and contributors from one place.</small>
                </div>
                <a href="{{ route('course.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>New Course
                </a>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <h5 class="card-title">{{__('nav.data_tbl')}}</h5>

                    <!-- Table with stripped rows -->
                    <table class="table datatable table-hover">
                        <thead>
                        <tr>
                            <th class="text-center"><b>{{__('NO.')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_name')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_type')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_state')}}</b></th>
                            <th class="text-center"><b>{{__('total enrolls')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_fee')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_creator')}}</b></th>
                            <th class="text-center"><b>{{__('course.label_approver')}}</b></th>
                            <th class="text-center"><b>{{__('btnText.action')}}</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($courses->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    No course found yet. Create your first course to start lesson publishing.
                                </td>
                            </tr>
                        @endif
                        @foreach($courses as $i => $course)
                            <tr>
                                <td>{{$loop->iteration}}</td>

                                <td class="hover-name" data-toggle="tooltip" title="{{__('nav.click_to_see_dtl')}}" onclick="window.location='{{url('course/'.$course->id.'/edit')}}';">
                                    <x-initials-avatar
                                        :src="$course->image ? asset('/storage/'.$course->image) : null"
                                        :name="$course->title"
                                        size="25"
                                        class="border border-success me-3"
                                        img-class="rounded-circle"
                                    />
                                    {{ $course->title }}
                                </td>

                                <td class="text-center">
                                    @if($course->courseType === CourseTypeEnums::BASIC->value)
                                        <span class="badge text-bg-primary">{{__('course.label_t_basic')}}</span>
                                    @else
                                        <span class="badge text-bg-info">{{__('course.label_t_advanced')}}</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($course->state === CourseStateEnums::PENDING->value)
                                        <span class="badge text-bg-warning">{{__('course.label_state_pen')}}</span>
                                    @else
                                        <span class="badge text-bg-success">{{__('course.label_state_app')}}</span>
                                    @endif
                                </td>

                                <td class="text-center"><strong>{{ $course->enrollCourses->count() }}</strong></td>

                                <td class="text-end"><strong>{{ $course->fees }} ks</strong></td>

                                <td>{{ $course->creator->name}} </td>

                                <td>
                                    @if($course->approver != null)
                                        {{ $course->approver->name}}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown custom-dropdown">
                                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="dropdown-text"><i class="bi bi-activity"></i></span>
                                        </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                @if(isCreatorOfAdmin($course))
                                                <li class="dropdown-item d-flex justify-content-between align-items-center">
                                                    <form action="{{route('course.destroy',$course->id)}}" class="w-100" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button onclick="return confirm('Are You Sure 🤨')" class="btn btn-danger w-100">
                                                            <i class="bi bi-journal-x me-3"></i>{{__('btnText.delete')}}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li class="dropdown-item d-flex justify-content-between align-items-center">
                                                    <div class="w-100">
                                                        <button class="btn border-0 btn-secondary w-100" title="share to others" data-bs-toggle="modal" data-bs-target="#share{{$i}}" data-bs-whatever="share">
                                                            <i class="bi bi-share"></i> {{__('btnText.share')}}
                                                        </button>
                                                    </div>
                                                </li>
                                                @endif
                                                <li class="dropdown-item d-flex justify-content-between align-items-center">
                                                    <div class="w-100">
                                                        <button class="btn border-0 btn-secondary w-100" onclick="window.location='{{url('lesson/'.$course->id.'/createForm')}}';">
                                                            <i class="bi bi-file-earmark-richtext"></i> {{__('course.create_ls')}}
                                                        </button>
                                                    </div>
                                                </li>
                                            </ul>
                                            <!-- Modal -->
                                            <div class="modal fade" id="share{{$i}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <h5>Giving Access as Contributor</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <small class="text-info">*** User who with teacher role are only allow.</small>
                                                            <form method="post" action="{{route('contributor.store')}}">
                                                                @csrf
                                                                @method('POST')
                                                                <div class="row mb-3">
                                                                    <input type="hidden" value="{{$course->id}}" name="course_id">
                                                                    <input type="email" placeholder="m@gmail.com" name="email" class="form-control">
                                                                </div>
                                                                <div class="text-center">
                                                                    <button type="submit" class="mb-3">
                                                                        <i class="bi bi-share-fill"></i> Share <span class="badge text-bg-primary">{{ $course->contribute_courses()->count() }}</span>
                                                                    </button>
                                                                </div>
                                                            </form>

                                                            {{--list of users who get access this to view--}}
                                                            <ul class="list-group text-start">
                                                                @foreach($course->contribute_courses as $j => $contributeCourse)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                        {{$j+1 .". ". $contributeCourse->user->name . " (" . $contributeCourse->user->email . ")"}}
                                                                        <form action="{{route('contributor.destroy',$contributeCourse->id)}}" method="post">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" onclick="return confirm('Are you sure? This schedule will be completely deleted.')"><i class="bi bi-trash"></i></button>
                                                                        </form>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
