@extends('admin.layouts.app')

@section('content')
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
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{__('nav.data_tbl')}}</h5>

                    <!-- Table with stripped rows -->
                    <table class="table datatable table-hover">
                        <thead>
                        <tr>
                            <th><b>{{__('users.no.')}}</b></th>
                            <th><b>{{__('users.name')}}</b></th>
                            <th><b>{{__('users.mail')}}</b></th>
                            <th><b>{{__('users.pts')}}</b></th>
                            <th><b>{{__('users.role')}}</b></th>
                            <th data-type="date" data-format="YYYY/DD/MM">{{__('users.dob')}}</th>
                            <th><b>{{__('btnText.action')}}</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $i => $user)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td class="hover-name" data-toggle="tooltip" title="{{__('nav.click_to_see_dtl')}}" onclick="window.location='{{ route('user.dtl.show', $user) }}';">
                                    <img src="{{asset('/storage/'.$user->avatar)}}" style="width: 25px; height: 25px" class="border rounded-5 border-success me-3" alt="profile">
                                    {{ $user->name }}
                                </td>
                                <td class="">{{ $user->email }}</td>
                                <td class="text-danger">{{ $user->points }}</td>
                                <td>
                                    <form action="{{ url('user/role/'.$user->id.'/update') }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="user_role" onchange="this.form.submit()">
                                            @foreach(\App\Enums\UserRoleEnums::getValues() as $value)
                                                <option value="{{ $value }}" {{ $user->role == $value ? 'selected' : '' }}>
                                                    {{ $value }} </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->birthdate)->format('Y/m/d') }}</td>
                                <td>
                                    <form action="{{url('user/dtl/'.$user->id)}}" method="post">
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
