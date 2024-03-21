<!-- ======= Sidebar ======= -->
@php
    use App\Enums\UserRoleEnums;

    $authUser = auth()->user()->role->value;
@endphp
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item" >
            <a class="nav-link collapsed {{is_active_route('dashboard')}}" href="{{route('dashboard')}}">
                <i class="bi bi-grid" ></i>
                <span >{{__('nav.dashboard')}}</span>
            </a>
        </li><!-- End Dashboard Nav -->

        @if($authUser === UserRoleEnums::ADMIN->value || $authUser === UserRoleEnums::TEACHER->value)
            <li class="nav-heading">{{__('nav.import_data')}}</li>
            <li class="nav-item">
                <a class="nav-link {{is_active_route(['course.create','course.index','course.show'])}}" data-bs-target="#course" data-bs-toggle="collapse" href="#">
                    <i class="bi {{is_active_route_val(['course.create','course.index','course.show'], 'bi-journal-bookmark-fill active','bi-journal-bookmark')}}"></i>
                    <span>{{__('course.title')}}</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="course" class="nav-content collapse {{is_active_route_val(['course.create'], 'show', '')}}" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{route('course.create')}}" class="{{is_active_route('course.create')}}">
                            <i class="bi bi-circle"></i><span>{{__('course.entry_title')}}</span>
                        </a>
                        <a href="{{route('course.index')}}" class="{{is_active_route('course.index')}}">
                            <i class="bi bi-circle"></i><span>{{__('course.list_title')}}</span>
                        </a>
                    </li>

                </ul>
                {{--end course--}}

                <a class="nav-link {{is_active_route(['lesson.index'])}}" data-bs-target="#lesson" data-bs-toggle="collapse" href="#">
                    <i class="bi {{is_active_route_val(['lesson.index'], 'bi-file-earmark-richtext-fill active','bi-file-earmark-richtext')}}"></i>
                    <span>{{__('lesson.title')}}</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="lesson" class="nav-content collapse {{is_active_route_val(['lesson.index'], 'show', '')}}" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{route('lesson.index')}}" class="{{is_active_route('lesson.index')}}">
                            <i class="bi bi-circle"></i><span>{{__('lesson.entry_title')}}</span>
                        </a>

                    </li>

                </ul>
            </li>
        @endif
        <!-- End Important Data-->

        @if($authUser === UserRoleEnums::ADMIN->value)
            <li class="nav-heading">{{__('nav.payment')}}</li>
            <li class="nav-item">
                <a class="nav-link {{is_active_route([''])}}" data-bs-target="#currency_exchange" data-bs-toggle="collapse" href="#">
                    <i class="bi {{is_active_route_val([''], 'bi-currency-exchange active','bi-currency-exchange')}}"></i>
                    <span>{{__('nav.currency_ex')}}</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="currency_exchange" class="nav-content collapse {{is_active_route_val([''], 'show', '')}}" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="#" class="{{is_active_route('')}}">
                            <i class="bi bi-circle"></i><span>{{__('nav.us_dol')}} To {{__('nav.mmk')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ is_active_route('') }}">
                            <i class="bi bi-circle"></i><span>{{__('nav.pts')}} To {{__('nav.mmk')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- End Payment Data-->


            <li class="nav-heading">{{__('nav.basic_data')}}</li>
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
            </li>
        @endif
        <!-- End Basic Data-->

        @if($authUser === UserRoleEnums::ADMIN->value)
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
        </li>
        @endif
        <!-- End Users Page Nav -->

        @if($authUser === UserRoleEnums::ADMIN->value)
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
        @endif
    </ul>

</aside><!-- End Sidebar-->
