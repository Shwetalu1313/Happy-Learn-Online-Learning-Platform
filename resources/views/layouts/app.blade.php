<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    use Illuminate\Support\Facades\Route;
        $currentRoute = Route::currentRouteName();
        $publicAppName = 'Happy Learn';
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $publicAppName }} | {{$titlePage}}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- icon --}}
    <link rel="stylesheet" href="{{asset('./assets/vendor/bootstrap-icons/bootstrap-icons.css')}}">


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

    <style>
        :root {
            --hl-bg: #060b16;
            --hl-surface: rgba(12, 18, 33, 0.88);
            --hl-surface-strong: #0f1a2f;
            --hl-text: #e8efff;
            --hl-muted: #9aa8c9;
            --hl-accent: #47d7ac;
            --hl-accent-2: #5aa6ff;
            --hl-border: rgba(116, 147, 209, 0.28);
            --hl-shadow: 0 16px 42px rgba(1, 8, 24, 0.36);
        }

        body {
            background:
                radial-gradient(1200px 600px at 10% -15%, rgba(44, 87, 255, 0.2), transparent 60%),
                radial-gradient(1000px 500px at 100% 0%, rgba(63, 189, 145, 0.17), transparent 55%),
                var(--hl-bg);
            color: var(--hl-text);
        }

        .hl-navbar {
            background: var(--hl-surface);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--hl-border);
            box-shadow: var(--hl-shadow);
            padding: 0.55rem 0;
            z-index: 1030;
            transition: padding 0.26s ease, background-color 0.26s ease, border-color 0.26s ease, box-shadow 0.26s ease;
        }

        .hl-navbar.is-compact {
            padding: 0.3rem 0;
            background: rgba(8, 13, 25, 0.95);
            border-bottom-color: rgba(126, 161, 232, 0.4);
            box-shadow: 0 14px 30px rgba(1, 8, 24, 0.44);
        }

        .hl-brand {
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            text-decoration: none;
            transition: transform 0.24s ease, letter-spacing 0.24s ease;
        }

        .hl-navbar.is-compact .hl-brand {
            transform: scale(0.97);
            letter-spacing: 0.01em;
        }

        .hl-brand:hover {
            color: #ffffff;
        }

        .hl-brand-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--hl-accent), var(--hl-accent-2));
            box-shadow: 0 0 0 6px rgba(71, 215, 172, 0.16);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .hl-brand:hover .hl-brand-dot {
            transform: scale(1.12) rotate(-9deg);
            box-shadow: 0 0 0 7px rgba(90, 166, 255, 0.2);
        }

        .hl-nav-link {
            color: #cbd8f7;
            font-weight: 600;
            font-size: 0.96rem;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
        }

        .hl-nav-link:hover,
        .hl-nav-link.active {
            color: #ffffff;
            background: rgba(90, 166, 255, 0.18);
            transform: translateY(-1px);
        }

        .hl-nav-link::after {
            content: "";
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 4px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--hl-accent), var(--hl-accent-2));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.22s ease;
        }

        .hl-nav-link:hover::after,
        .hl-nav-link.active::after {
            transform: scaleX(1);
        }

        .hl-nav-toggler {
            border-color: rgba(167, 193, 244, 0.4);
        }

        .hl-nav-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(90, 166, 255, 0.25);
        }

        .hl-search {
            background: rgba(7, 12, 24, 0.72);
            border: 1px solid var(--hl-border);
            border-radius: 12px;
            padding: 0.28rem;
            min-width: 260px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .hl-search:focus-within {
            border-color: rgba(133, 182, 255, 0.85);
            box-shadow: 0 0 0 3px rgba(90, 166, 255, 0.18);
            transform: translateY(-1px);
        }

        .hl-search-wrap {
            width: 100%;
        }

        @media (min-width: 992px) {
            .hl-search-wrap {
                width: auto;
            }
        }

        .hl-search .form-control {
            background: transparent;
            border: 0;
            color: #e5edff;
            min-width: 0;
        }

        .hl-search .form-control:focus {
            box-shadow: none;
        }

        .hl-search .form-control::placeholder {
            color: #8ea0c7;
        }

        .hl-search .btn {
            border-radius: 9px;
            border-color: rgba(143, 171, 230, 0.42);
            color: #d9e6ff;
        }

        .hl-icon-btn {
            color: #dbe7ff;
            border-radius: 11px;
            padding: 0.38rem 0.56rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .hl-icon-btn:hover {
            color: #ffffff;
            border-color: rgba(155, 185, 244, 0.38);
            background: rgba(83, 128, 214, 0.18);
            transform: translateY(-1px);
        }

        .hl-dropdown {
            background: #0f1b31;
            border: 1px solid var(--hl-border);
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
            padding: 0.4rem;
        }

        .hl-dropdown .dropdown-item {
            color: #d5e3ff;
            border-radius: 10px;
            font-size: 0.94rem;
        }

        .hl-dropdown .dropdown-item:hover {
            background: rgba(90, 166, 255, 0.18);
            color: #ffffff;
        }

        .hl-footer {
            margin-top: 3.5rem;
            background:
                radial-gradient(800px 320px at 0% -20%, rgba(69, 132, 255, 0.22), transparent 62%),
                radial-gradient(600px 260px at 100% 0%, rgba(59, 203, 162, 0.18), transparent 55%),
                #070d1b;
            border-top: 1px solid rgba(128, 159, 222, 0.2);
            color: #d3def8;
        }

        .hl-footer-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.02rem;
            letter-spacing: 0.01em;
        }

        .hl-footer-text,
        .hl-footer-link {
            color: #9fb0d7;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .hl-footer-link:hover {
            color: #ffffff;
        }

        .hl-social {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #cfe0ff;
            border: 1px solid rgba(154, 181, 239, 0.34);
            transition: all 0.2s ease;
        }

        .hl-social:hover {
            color: #ffffff;
            border-color: rgba(111, 171, 255, 0.8);
            background: rgba(87, 153, 245, 0.2);
            transform: translateY(-2px);
        }

        .hl-footer-bottom {
            border-top: 1px solid rgba(123, 152, 206, 0.2);
            color: #90a5d0;
            font-size: 0.88rem;
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

    @yield('styles')
</head>

<body data-bs-theme="dark">
    <div id="app">
        <nav class="navbar navbar-expand-lg hl-navbar sticky-top">
            <div class="container-xl">
                <a class="navbar-brand hl-brand fs-4" href="{{ url('/') }}">
                    <span class="hl-brand-dot"></span>
                    {{ $publicAppName }}
                </a>
                <button class="navbar-toggler hl-nav-toggler" type="button" data-bs-toggle="collapse"
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
    <script src="{{asset('./assets/vendor/quill/quill.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nav = document.querySelector('.hl-navbar');
            if (!nav) {
                return;
            }

            const threshold = 16;
            let ticking = false;

            const setCompactState = () => {
                nav.classList.toggle('is-compact', window.scrollY > threshold);
                ticking = false;
            };

            setCompactState();
            window.addEventListener('scroll', function () {
                if (ticking) {
                    return;
                }
                ticking = true;
                window.requestAnimationFrame(setCompactState);
            }, { passive: true });
        });
    </script>

        @yield('scripts')


</body>

</html>
