@extends('admin.layouts.app')
@section('content')
    @php
        use App\Models\CourseEnrollUser;
        $enrolls = CourseEnrollUser::getEnrollmentsForUser(auth()->user());
    @endphp
    <div class="card">
        <div class="card-body">

            <table class="table datatable">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>User Name</th>
                    <th>User Mail</th>
                    <th>Course Name</th>
                    <th>Pay By</th>
                    <th>Card No.</th>
                    <th data-type="date" data-format="YYYY/DD/MM">Enroll At</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($enrolls as $i => $enroll)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>{{$enroll->user->name}}</td>
                            <td>{{$enroll->user->email}}</td>
                            <td><a href="{{url('course/'.$enroll->course_id.'/edit')}}">{{$enroll->course->title}}</a></td>
                            <td>{{$enroll->payment_type}}</td>
                            <td>{{$enroll->card_number}}</td>
                            <td>{{ \Carbon\Carbon::parse($enroll->created_at)->format('Y/m/d') }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
