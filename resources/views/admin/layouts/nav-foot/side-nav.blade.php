<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item" >
            <a class="nav-link collapsed {{is_active_route('dashboard')}}" href="{{route('dashboard')}}">
                <i class="bi bi-grid" ></i>
                <span >{{__('nav.dashboard')}}</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link {{is_active_route(['category.index','category.lst_V1','sub_category.create'])}}" data-bs-target="#category_page" data-bs-toggle="collapse" href="#">
                <i class="bi {{is_active_route_val(['category.index','category.lst_V1','sub_category.create'], 'bi-box-seam-fill active','bi-box-seam')}}"></i>
                <span>{{__('cate.cates')}}</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="category_page" class="nav-content collapse {{is_active_route_val(['category.index'], 'show', '')}}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('category.index')}}" class="{{is_active_route('category.index')}}">
                        <i class="bi bi-circle"></i><span>{{__('cate.cate_ent')}}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.lst_V1') }}" class="{{ is_active_route('category.lst_V1') }}">
                        <i class="bi bi-circle"></i><span>{{ __('cate.cate_lst') }}</span>
                    </a>
                </li>
                <hr>
                <li>
                    <a href="{{route('sub_category.create')}}" class="{{is_active_route('sub_category.create')}}">
                        <i class="bi bi-circle"></i><span>{{__('cate.sub_cate_ent')}}</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('sub_category.index')}}" class="{{is_active_route('sub_category.index')}}">
                        <i class="bi bi-circle"></i><span>{{__('cate.sub_cate_lst')}}</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Category Page Nav -->

        <li class="nav-item">
            <a class="nav-link " data-bs-target="#user_page" data-bs-toggle="collapse" href="#">
                <i class="bi {{is_active_route_val(['user.role.index','user.role.bulkInsert','user.dtl.index'], 'bi-people-fill','bi-people')}}"></i>
                <span>{{__('users.users')}}</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="user_page" class="nav-content collapse {{is_active_route_val(['user.role.index','user.role.bulkInsert'], 'show', '')}}" data-bs-parent="#sidebar-nav">

                <li>
                    <a href="{{route('user.dtl.index')}}" class="{{is_active_route('user.dtl.index')}}">
                        <i class="bi bi-circle"></i><span>{{__('users.user_lst')}}</span>
                    </a>
                </li>

            </ul>
        </li><!-- End Users Page Nav -->

        <li class="nav-heading">{{__('nav.extra')}}</li>

        <li class="nav-item">
            <a class="nav-link " data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>{{__('jobapplication.job')}}</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse {{is_active_route_collapse_show(['job.post','job.list'])}}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('job.post')}}" class="{{is_active_route('job.post')}}">
                        <i class="bi bi-circle"></i><span>{{__('nav.j_post_f')}}</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('job.list')}}" class="{{is_active_route('job.list')}}">
                        <i class="bi bi-circle"></i><span>{{__('nav.j_post_l')}}</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Job Page Nav -->

    </ul>

</aside><!-- End Sidebar-->