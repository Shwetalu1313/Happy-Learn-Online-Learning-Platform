<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{route('dashboard')}}" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block">Happy Learn:M</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="{{ route('global.search') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Global Search" title="Enter search keyword" minlength="2">
            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item">
                @include('language-switch')
            </li>

            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            @php
                $latestNotifications = Auth::user()->notifications()->latest()->take(6)->get();
                $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    @if($unreadNotificationsCount > 0)
                        <span class="badge bg-primary badge-number">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>You have {{ $unreadNotificationsCount }} new notifications</span>
                        <a href="{{ route('notifications.index') }}">
                            <span class="badge rounded-pill bg-primary p-2 ms-2">View all</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    @forelse($latestNotifications as $notification)
                        @php
                            $title = $notification->data['title'] ?? 'Notification';
                            $line = $notification->data['line'] ?? 'You have a new update.';
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        <li class="notification-item {{ $isUnread ? 'bg-light' : '' }}">
                            <i class="bi bi-info-circle text-primary"></i>
                            <div class="w-100">
                                <h4 class="mb-1">{{ $title }}</h4>
                                <p class="mb-1">{{ $line }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                                    @if($isUnread)
                                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                            <button type="submit" class="btn btn-link btn-sm p-0">Mark read</button>
                                        </form>
                                    @endif
                                </div>
                                <a class="small text-primary" href="{{ route('notifications.open', $notification->id) }}">Open</a>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    @empty
                        <li class="notification-item">
                            <i class="bi bi-bell-slash text-muted"></i>
                            <div>
                                <h4>No notifications</h4>
                                <p class="mb-0">You are all caught up.</p>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    @endforelse

                    <li class="dropdown-footer d-flex justify-content-between align-items-center px-3">
                        <a href="{{ route('notifications.index') }}">Show all notifications</a>
                        @if($unreadNotificationsCount > 0)
                            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                @csrf
                                <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                <button type="submit" class="btn btn-link btn-sm p-0">Mark all</button>
                            </form>
                        @endif
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown">

                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-chat-left-text"></i>
                    <span class="badge bg-success badge-number">3</span>
                </a><!-- End Messages Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                    <li class="dropdown-header">
                        You have 3 new messages
                        <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="{{asset('/storage/webstyle/img/messages-1.jpg')}}" alt="" class="rounded-circle">
                            <div>
                                <h4>Maria Hudson</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>4 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="/storage/webstyle/img/messages-2.jpg" alt="" class="rounded-circle">
                            <div>
                                <h4>Anna Nelson</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>6 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="message-item">
                        <a href="#">
                            <img src="/storage/webstyle/img/messages-3.jpg" alt="" class="rounded-circle">
                            <div>
                                <h4>David Muldon</h4>
                                <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                <p>8 hrs. ago</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li class="dropdown-footer">
                        <a href="#">Show all messages</a>
                    </li>

                </ul><!-- End Messages Dropdown Items -->

            </li><!-- End Messages Nav -->

            <li class="nav-item dropdown pe-3">

                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#admin.profile" data-bs-toggle="dropdown">
                    <x-initials-avatar
                        :src="Auth::user()->avatar ? asset('storage/' . ltrim(Auth::user()->avatar, '/')) : null"
                        :name="Auth::user()->name"
                        size="36"
                        class="border border-1 border-secondary-subtle"
                        img-class="rounded-circle"
                    />
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{Auth::user()->name}}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{Auth::user()->name}}</h6>
                        <span>{{Auth::user()->role}}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('user.dtl.show', Auth::user()) }}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('user.dtl.show', Auth::user()) }}">
                            <i class="bi bi-gear"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @if(Auth::user()->role->value === \App\Enums\UserRoleEnums::ADMIN->value)
                        <li class="dropdown-header">
                            <h6 class="mb-0">Settings</h6>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.notifications.config') }}">
                                <i class="bi bi-bell"></i>
                                <span>Notification Config</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.sso.providers.index') }}">
                                <i class="bi bi-shield-lock"></i>
                                <span>SSO Config</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('activities') }}">
                                <i class="bi bi-gear-wide-connected"></i>
                                <span>Activities Logs</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    @endif

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                            <i class="bi bi-question-circle"></i>
                            <span>Need Help?</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <i class="bi bi-box-arrow-right"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->
