@extends('layouts.app')

@section('content')
    @php
    $titlePage = 'Opportunity Search';
 @endphp
    <div class="container-fluid pt-5">
        <h1 class="text-capitalize text-center mb-5">{{ __('nav.j_post_l') }}</h1>

        <div class="container">
            {{-- Filter Search Bar --}}
            <div class="form-group mb-5">
                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
            </div>

            <ul class="list-group">
                @foreach($jobs as $index => $job)
                    <a href="{{ url('job/'.$job->id.'/detail') }}" class="list-group-item list-group-item-action mb-3">
                        <span class="badge badge-primary ms-2 fs-3">{{ $index + 1 }}</span>{{ $job->title }}
                    </a>
                @endforeach
            </ul>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Filter Functionality without interacting with backend action
            $('#searchInput').on('input', function() {
                const searchText = $(this).val().toLowerCase();
                $('.list-group-item').each(function() {
                    const title = $(this).text().toLowerCase();
                    if (title.includes(searchText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endsection
