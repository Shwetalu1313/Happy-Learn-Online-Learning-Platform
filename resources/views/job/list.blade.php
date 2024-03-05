@extends('admin.layouts.app')
@section('content')
    @if(session('success'))
        <div class="alert alert-danger">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Datatable</h5>

                    <!-- Table with stripped rows -->
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>NO.</th>
                            <th><b>Title</b></th>
                            <th data-type="date" data-format="YYYY/DD/MM">Open Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jobs as $i => $job)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td onclick="redirectToJobShow('{{ route('job.show', ['JobPost' => $job]) }}')" style="cursor: pointer">{{ $job->title }}</td>
                                <td onclick="redirectToJobShow('{{ route('job.show', ['JobPost' => $job]) }}')">{{ $job->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <form action="{{url('job/'.$job->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are You Sure 🤨')" class="btn btn-danger">{{__('btnText.delete')}}</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <script>
                        function redirectToJobShow(url) {
                            window.location.href = url;
                        }
                    </script>
                    <!-- End Table with stripped rows -->

                </div>
            </div>

        </div>
    </div>
@endsection
