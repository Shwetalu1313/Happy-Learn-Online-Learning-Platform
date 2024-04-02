<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{$titlePage}}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

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
        @yield('content')
    </section>

</main><!-- End #main -->


<!-- ======= Footer ======= -->
@include('admin.layouts.nav-foot.footer')
<!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{asset('./assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('./assets/vendor/chart.js/chart.umd.js')}}"></script>
<script src="{{asset('./assets/vendor/echarts/echarts.min.js')}}"></script>
<script src="{{asset('./assets/vendor/quill/quill.min.js')}}"></script>
<script src="{{asset('./assets/vendor/simple-datatables/simple-datatables.js')}}"></script>
<script src="{{asset('./assets/vendor/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('./assets/vendor/php-email-form/validate.js')}}"></script>
<script src="{{asset('./assets/js/ImageLargeSizeTracker.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.11/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.27.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@0.1.1"></script>


<!-- Template Main JS File -->
{{--  <script src="{{'./assets/js/main.js'}}"></script>--}}
@php
    use Illuminate\Support\Facades\Route;

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

<script>
    $(document).ready(function() {
        // URL of your Laravel route
        var apiUrl = "http://127.0.0.1:8000/fetch-data";

        // Make AJAX request
        $.ajax({
            url: apiUrl,
            type: "GET",
            dataType: "json",
            success: function(data) {
                // Handle the response data
                console.log(data); // You can replace this with your own handling logic
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error("Error fetching data:", error);
            }
        });
    });

</script>
@yield('scripts')


</body>

</html>
