<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    use Illuminate\Support\Facades\Route;
        $currentRoute = Route::currentRouteName();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | {{$titlePage}}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    {{-- icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">


    <link href="{{asset('./assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/css/animationButton.css')}}" rel="stylesheet">
    <link href="{{asset('./assets/css/other.css')}}" rel="stylesheet">
    @if($currentRoute === 'home')
        <link href="{{asset('./assets/css/onlyHome.css')}}" rel="stylesheet">
    @endif


    <!-- vite scss and js -->
    @vite([
    'resources/css/app.css',
    'resources/sass/app.scss',
    'resources/js/app.js',
    'resources/css/quill_val.css',
    ])


    @yield('styles')
</head>

<body data-bs-theme="dark">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg--second shadow-sm">
            <div class="container ">
                <a class="navbar-brand fs-3" style="color: aqua;" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @include('layouts.left_nav')

                    <!-- Right Side Of Navbar -->
                    @include('layouts.right_nav')
                </div>
            </div>
        </nav>

        <main class="mb-2 {{is_active_route_val(['home'],'primaryInfo-dark-gradient','')}}">

            @yield('content')
        </main>


        {{-- footer start --}}
        @include('layouts.footer')

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{asset('./assets/vendor/quill/quill.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if (Auth::check())
            <script>
            const userImageElement = document.getElementById("user_image");

            if (userImageElement) {
                axios
                    .get("/api/user/{{ Auth::id() }}")
                    .then(function(response) {
                        const userData = response.data;
                        const imageUrl = userData.avatar;
                        if (userData.avatar) {
                            userImageElement.src = imageUrl;
                        }
                        console.log("User Data: success");
                    })
                    .catch(function(error) {
                        console.error("Error fetching user data:", error);
                    });
            }
            </script>
        @endif

        @yield('scripts')


</body>

</html>
