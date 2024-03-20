<ul class="navbar-nav me-auto fs-5 l-nv">
    <li class="nav-item"><a href="{{ route('home') }}" class="nav-link {{is_active_route(['home'])}}">{{ __('nav.home') }}</a></li>
    <li class="nav-item"><a href="{{ route('job.intro') }}" class="nav-link {{is_active_route(['job.intro','job.listV2','job.detail'])}}">{{ __('nav.opportunities') }}</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{is_active_route(['users.top_pts'])}}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{__('nav.abt')}}
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item {{is_active_route(['users.top_pts'])}}" href="{{route('users.top_pts')}}">{{__('nav.tpu')}}</a></li>
            <li><a class="dropdown-item {{is_active_route(['users.teachers'])}}" href="{{route('users.teachers')}}">{{__('nav.teacher_lst')}}</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </li>
</ul>
