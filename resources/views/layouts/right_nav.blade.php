<ul class="navbar-nav ms-auto">
    <li class="nav-item my-auto">
        @include('language-switch')
    </li>
    <!-- Authentication Links -->
    @guest
        @if (Route::has('login'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('nav.login') }}</a>
            </li>
        @endif

        @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('nav.register') }}</a>
            </li>
        @endif
    @else
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                @if (Auth::user()->avatar)
                    <img id="user_image" class="image rounded-circle"
                         src="{{ '/storage/avatars/' . Auth::user()->image }}" alt="profile_image"
                         style="width: 60px;height: 60px; padding: 10px; margin: 0px; ">
                @endif

            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <div class="points-container">
                    <div class="points">
                        <p class="points-value">{{ Auth::user()->points }}</p>
                    </div>
                    <div class="points-info tooltip">
                        wtf
                    </div>
                    <div class="points-progress">
                        pts
                    </div>
                </div>
                <a class="dropdown-item ps-3" href="{{route('user.profile', [Auth::user()->id])}}"><i class="bi-person fs-4 me-3"></i>{{__('users.profile')}}</a>
                <a class="dropdown-item ps-3" href="{{ route('user.dashboard') }}"><i class="bi bi-window me-3"></i>{{__('nav.dashboard')}}</a>
                <a class="dropdown-item ps-3" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi-door-open fs-4 me-3"></i>{{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

        </li>
    @endguest
</ul>

@section('scripts')
    <script>
        $(document).ready(function() {
            // Get the initial points value
            const initialValue = parseInt($("#points-value").text());

            // Set up the CountUp instance
            const countUp = new CountUp('points-value', initialValue);

            // Start the animation
            countUp.start();
        });
    </script>

@endsection
