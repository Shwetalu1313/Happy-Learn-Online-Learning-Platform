<ul class="navbar-nav ms-lg-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
    @auth
        @php
            $latestNotifications = Auth::user()->notifications()->latest()->take(6)->get();
            $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
        @endphp

        <li class="nav-item hl-search-wrap">
            <form action="{{ route('global.search') }}" method="GET" class="d-flex hl-search">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control form-control-sm"
                    placeholder="Search courses, teachers, jobs..."
                    minlength="2"
                >
                <button type="submit" class="btn btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link hl-icon-btn position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                @if($unreadNotificationsCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                    </span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end hl-dropdown p-0" style="min-width: 340px; max-width: 380px;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-secondary-subtle">
                    <div>
                        <strong class="text-light">Notifications</strong>
                        <div class="small text-secondary">{{ $unreadNotificationsCount }} unread</div>
                    </div>
                    @if($unreadNotificationsCount > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                            <button class="btn btn-sm btn-outline-light" type="submit">Mark all</button>
                        </form>
                    @endif
                </div>

                @forelse($latestNotifications as $notification)
                    @php
                        $title = $notification->data['title'] ?? 'Notification';
                        $line = $notification->data['line'] ?? 'You have a new update.';
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <div class="px-3 py-2 border-bottom border-secondary-subtle {{ $isUnread ? 'bg-primary bg-opacity-10' : '' }}">
                        <a class="text-decoration-none d-block mb-1" href="{{ route('notifications.open', $notification->id) }}">
                            <div class="fw-semibold text-light">{{ $title }}</div>
                            <div class="small text-secondary">{{ $line }}</div>
                        </a>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                            @if($isUnread)
                                <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                    <button class="btn btn-sm btn-link p-0 text-info" type="submit">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-3 text-center text-secondary small">No notifications yet.</div>
                @endforelse

                <div class="px-3 py-2">
                    <a class="btn btn-sm btn-outline-light w-100" href="{{ route('notifications.index') }}">View all notifications</a>
                </div>
            </div>
        </li>
    @endauth

    <li class="nav-item">
        @include('language-switch')
    </li>

    @guest
        @if (Route::has('login'))
            <li class="nav-item">
                <a class="btn btn-outline-light btn-sm px-3" href="{{ route('login') }}">{{ __('nav.login') }}</a>
            </li>
        @endif

        @if (Route::has('register'))
            <li class="nav-item">
                <a class="btn btn-info btn-sm px-3" href="{{ route('register') }}">{{ __('nav.register') }}</a>
            </li>
        @endif
    @else
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link p-0" href="#" role="button"
               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                <x-initials-avatar
                    :src="Auth::user()->avatar ? asset('storage/' . ltrim(Auth::user()->avatar, '/')) : null"
                    :name="Auth::user()->name"
                    size="44"
                    class="border border-2 border-secondary-subtle"
                    img-class="rounded-circle"
                />
            </a>

            <div class="dropdown-menu dropdown-menu-end hl-dropdown" aria-labelledby="navbarDropdown">
                <div class="px-3 py-2 border-bottom border-secondary-subtle">
                    <div class="fw-semibold text-light">{{ Auth::user()->name }}</div>
                    <div class="small text-secondary">{{ __('users.pts') }}: {{ number_format(Auth::user()->points) }}</div>
                </div>
                <a class="dropdown-item" href="{{ route('user.profile', [Auth::user()->id]) }}"><i class="bi bi-person me-2"></i>{{ __('users.profile') }}</a>
                <a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="bi bi-grid me-2"></i>{{ __('nav.dashboard') }}</a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-door-open me-2"></i>{{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    @endguest
</ul>
