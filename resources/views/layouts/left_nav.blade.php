<ul class="navbar-nav me-auto mb-3 mb-lg-0 align-items-lg-center gap-lg-1">
    <li class="nav-item">
        <a href="{{ route('home') }}" class="nav-link hl-nav-link {{ is_active_route(['home']) }}">{{ __('nav.home') }}</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('course.list.learners') }}" class="nav-link hl-nav-link {{ is_active_route(['course.list.learners']) }}">{{ __('course.title') }}</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('job.intro') }}" class="nav-link hl-nav-link {{ is_active_route(['job.intro','job.listV2','job.detail']) }}">{{ __('nav.opportunities') }}</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link hl-nav-link dropdown-toggle {{ is_active_route(['users.top_pts','users.teachers']) }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('nav.abt') }}
        </a>
        <ul class="dropdown-menu hl-dropdown">
            <li><a class="dropdown-item {{ is_active_route(['users.top_pts']) }}" href="{{ route('users.top_pts') }}">{{ __('nav.tpu') }}</a></li>
            <li><a class="dropdown-item {{ is_active_route(['users.teachers']) }}" href="{{ route('users.teachers') }}">{{ __('nav.teacher_lst') }}</a></li>
        </ul>
    </li>
</ul>
