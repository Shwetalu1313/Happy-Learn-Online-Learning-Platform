<ul class="navbar-nav me-auto fs-5 l-nv">
    <li class="nav-item"><a href="{{ route('home') }}" class="nav-link {{is_active_route(['home'])}}">{{ __('nav.home') }}</a></li>
    <li class="nav-item"><a href="{{ route('job.intro') }}" class="nav-link {{is_active_route(['job.intro','job.listV2','job.detail'])}}">{{ __('nav.opportunities') }}</a></li>
</ul>
