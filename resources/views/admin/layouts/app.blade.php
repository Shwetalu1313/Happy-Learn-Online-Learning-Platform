<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{$titlePage}}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    @php
        use Illuminate\Support\Facades\Route;
        $currentRouteName = Route::currentRouteName();
        $needsApexCharts = in_array($currentRouteName, ['dashboard', 'admin.system-health.index'], true);
        $needsChartJs = $currentRouteName === 'exchange.edit';
    @endphp

    <!-- Favicons -->
    <link href="{{asset('/webstyle/img/favicon.png')}}" rel="icon">
    <link href="{{asset('/webstyle/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    {{--    bootstrap calling with vite --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/admin.js', 'resources/css/style.css'])

    <!-- Vendor CSS Files -->
    <link href="{{asset('./assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">

    <!-- Template Main CSS File -->
    {{--  <link href="{{asset('./assets/css/style.css')}}" rel="stylesheet">--}}
    <style>
        .sidebar-nav.nav-item a{
            text-decoration: none;
        }
        .sidebar-nav.nav-item.components-nav a{
            text-decoration: none;
        }
        .hover-name {
            cursor: pointer;
        }

        .hover-overlay {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
            opacity: 0; /* Initially hidden */
            transition: opacity 0.3s ease-in-out; /* Smooth transition */
        }

        .category-item:hover .hover-overlay {
            opacity: 1; /* Show on hover */
        }

        .softGradient {
            background: linear-gradient(72deg, rgba(97,247,47,0.1092086492800245) 23%, rgba(6,171,255,0.09520304703912819) 97%);
        }

        .initials-avatar {
            --ia-size: 40px;
            width: var(--ia-size);
            height: var(--ia-size);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border-radius: 9999px;
            background: linear-gradient(135deg, #1d3557, #274c77);
            color: #e7f1ff;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            user-select: none;
        }

        .initials-avatar__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .initials-avatar__fallback {
            width: 100%;
            height: 100%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: calc(var(--ia-size) * 0.38);
            line-height: 1;
        }

        .initials-avatar.show-fallback .initials-avatar__fallback {
            display: flex;
        }

        .initials-avatar.show-fallback .initials-avatar__img {
            display: none;
        }
    </style>
</head>

<body>

{{--    Header  --}}
@include('admin.layouts.nav-foot.header')
{{--  End Header--}}

{{--side nav --}}
@include('admin.layouts.nav-foot.side-nav')
{{--end side nav--}}


<main id="main" class="main">

    <div class="pagetitle">
        <h1>{{ $titlePage }}</h1>
    </div><!-- End Page Title -->


    <section class="section">
        <x-flash-messages />
        @yield('content')
    </section>

</main><!-- End #main -->


<!-- ======= Footer ======= -->
@include('admin.layouts.nav-foot.footer')
<!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
@if($needsApexCharts)
    <script src="{{asset('./assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
@endif

@if($needsChartJs)
    <script src="{{asset('./assets/vendor/chart.js/chart.umd.js')}}"></script>
@endif

<script src="{{asset('./assets/js/ImageLargeSizeTracker.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Template Main JS File -->
{{--  <script src="{{'./assets/js/main.js'}}"></script>--}}
@php
    $currentRouter = Route::currentRouteName();
    $data = '';

    $storingCondition = in_array($currentRouter, ['job.post', 'job.store', 'course.create', 'course.store', 'lesson.createForm', 'lesson.store']);
    $updatingCondition = in_array($currentRouter, ['job.show', 'job.update', 'course.edit']);

    if (isset($job) && isset($job->requirements)) {
        $dataJob = $job->requirements;
    } elseif (isset($course) && isset($course->description)) {
        $dataCourse = $course->description;
    }
    $data = isset($dataJob) ? $dataJob : (isset($dataCourse) ? $dataCourse : '');

@endphp

@if($storingCondition)
    <script>
        $('#btn-submit').on('click', () => {
            const body = $('.ql-editor').html();
            $('#req_input').val(body);
            console.log($('#req_input').val(body));

            const title = $('#title').text();
            $('#title_input').val(title);
        });
    </script>
@endif

@if($updatingCondition)
    <script>
        // Function to decode HTML entities
        function decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        $(document).ready(function () {
            $('.ql-editor').html(decodeHtml("{{ $data }}"));

            $('#btn-submit').on('click', () => {
                const body = $('.ql-editor').html();
                $('#req_input').val(body);

                const title = $('#title').text();
                $('#title_input').val(title);
            });
        });


    </script>
@endif

@yield('scripts')


</body>

</html>
