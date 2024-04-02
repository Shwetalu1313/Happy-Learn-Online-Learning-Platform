@extends('admin.layouts.app')
@section('content')
    @php
        use App\Models\SystemActivity;
        $activities = SystemActivity::getData(true, true);
    @endphp

    <div class="card">
        <div class="card-body">
            <table class="table datatable">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Model</th>
                    <th>short</th>
                    <th>About</th>
                    <th>user_id</th>
                    <th>route_name</th>
                    <th>ip address</th>
                    <th>user Agent</th>
                    <th data-type="date" data-format="YYYY/DD/MM">date</th>
                </tr>
                </thead>

                <tbody>
                    @foreach($activities as $i => $activity)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>{{$activity->table_name}}</td>
                            <td>{{$activity->short}}</td>
                            <td>{{$activity->about}}</td>
                            <td>{{$activity->user_id}}</td>
                            <td>{{$activity->route_name}}</td>
                            <td>{{$activity->ip_address}}</td>
                            <td>{{$activity->user_agent}}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('Y/m/d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
