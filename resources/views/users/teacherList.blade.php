@extends('layouts.app')
@section('content')
    @php
        use App\Models\User;
        $teachers = User::teachers();
    @endphp

    <div class="container py-5">
        <h2 class="text-center">{{$titlePage}}</h2>
        <hr class="w-25 text-center mx-auto">

        <div class="row row-cols-1 row-cols-md-3 g-3">
            @foreach($teachers as $teacher)
                <div class="col mb-3">
                    <div class="card">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <img src="{{asset('storage/'.$teacher->avatar)}}" class="img-fluid mx-auto mb-3" style="max-height: 200px;">
                            <h4>{{$teacher->name}}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
