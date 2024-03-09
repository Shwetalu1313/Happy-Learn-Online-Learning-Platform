@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body table-responsive">
                    <h5 class="card-title">{{__('nav.data_tbl')}}</h5>

                    <!-- Table with stripped rows -->
                    <table class="table datatable table-hover">
                        <thead>
                        <tr>
                            <th><b>{{__('NO.')}}</b></th>
                            <th><b>{{__('course.label_name')}}</b></th>
                            <th><b>{{__('course.label_type')}}</b></th>
                            <th><b>{{__('course.label_state')}}</b></th>
                            <th><b>{{__('course.label_fee')}}</b></th>
                            <th><b>{{__('course.label_creator')}}</b></th>
                            <th><b>{{__('course.label_approver')}}</b></th>
                            <th><b>{{__('btnText.action')}}</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td class="hover-name" data-toggle="tooltip" title="{{__('nav.click_to_see_dtl')}}" onclick="window.location='';">
                                    <img src="{{asset('/storage/'.$course->image)}}" style="width: 25px; height: 25px" class="border rounded-5 border-success me-3" alt="profile">
                                    {{ $course->title }}
                                </td>
                                <td class="">
                                    @if($course->courseType === \App\Enums\CourseTypeEnums::BASIC->value)
                                        <span class="badge text-bg-primary">{{__('course.label_t_basic')}}</span>
                                    @else
                                        <span class="badge text-bg-info">{{__('course.label_t_advanced')}}</span>
                                    @endif
                                </td>
                                <td class="">
                                    @if($course->state === \App\Enums\CourseStateEnums::PENDING->value)
                                        <span class="badge text-bg-warning">{{__('course.label_state_pen')}}</span>
                                    @else
                                        <span class="badge text-bg-success">{{__('course.label_state_app')}}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $course->fees }} ks</strong></td>
                                <td>{{ $course->creator->email}} </td>
                                <td>
                                    @if($course->approver != null)
                                        {{ $course->approver->email}}
                                    @endif
                                </td>
                                <td>
                                    <form action="{{route('course.destroy',$course->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are You Sure 🤨')" class="btn btn-danger">{{__('btnText.delete')}}</button>
                                    </form>
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
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
