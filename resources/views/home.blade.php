@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if (session('login'))
        <div class="alert alert-success" role="alert">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="text-warning">{{Auth::user()->name}}</strong> Welcome again. You are log in.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>
@endsection
