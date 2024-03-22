@extends('layouts.app')
@section('content')
    @php $titlePage = $job->title.' \' Job Detail \''; @endphp
    <section class="container py-5">
        <h1 class="mb-5 text-forth">{{__('jobapplication.job_about')}}</h1>
        <div class="container-fluid border border-primary p-3 rounded-3">
            <h3 class="mb-5 text-center fs-3">{{$job->title}}</h3>
            <hr style="width: 50%" class="mx-auto">
            <div class="border-1 border-success-subtle fs-5" id="req"></div>
        </div>
    </section>

@endsection

@section('scripts')
    <script>
        function decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        $(document).ready(function () {
            $('#req').html(decodeHtml("{{ $job->requirements }}"));

        })
    </script>
@endsection
