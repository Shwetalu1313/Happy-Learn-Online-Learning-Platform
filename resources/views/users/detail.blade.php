@extends(is_active_route_val('user.dtl.show','admin.layouts.app','layouts.app'))

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <section class="section profile {{is_active_route_val('user.profile', 'container','')}}">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="{{asset('storage/'.$user->avatar)}}" alt="Profile" class="rounded-circle {{is_active_route_val('user.profile','img-fluid','')}}">
                        <h2>{{$user->name}}</h2>
                        <h3>{{$user->role}}</h3>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">{{__('btnText.over_v')}}</button>
                            </li>
                            @if(Auth::check() && Auth::user()->id == $user->id)
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">{{__('btnText.edt')}}</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">{{__('nav.abt')}}</h5>
                                <p class="small fst-italic {{is_active_route_val('user.profile', 'text-success','')}}">
                                    @if (!$user->about)
                                        <span class="{{is_active_route_val('user.profile', 'text-success','text-black-50')}}" >{{__('nav.no_abt')}}</span>
                                    @else
                                        {{$user->about}}
                                    @endif
                                </p>

                                <h5 class="card-title">{{__('users.profile_dtl')}}</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">{{__('users.name')}}</div>
                                    <div class="col-lg-9 col-md-8">{{$user->name}}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">{{__('users.pts')}}</div>
                                    <div class="col-lg-9 col-md-8 text-danger">{{$user->points}} <span class="{{is_active_route_val('user.profile', 'text-white','text-black')}}">pts</span></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">{{__('users.role')}}</div>
                                    <div class="col-lg-9 col-md-8 text-success">{{$user->role}}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">{{__('users.dob')}}</div>
                                    <div class="col-lg-9 col-md-8">{{$user->birthdate}}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">{{__('users.ph')}}</div>
                                    <div class="col-lg-9 col-md-8">{{$user->phone}}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">{{__('users.mail')}}</div>
                                    <div class="col-lg-9 col-md-8">{{$user->email}}</div>
                                </div>

                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                                <!-- Profile Edit Form -->
                                <form action="{{route(is_active_route_val('user.profile','user.profile.update','user.dtl.pf_update'))}}" id="profile_update" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">{{__('users.avatar')}}</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input type="file" id="image-input" accept="image/*" name="avatar" onchange="imagePreview(event)">
                                            <div id="image-preview"></div>
                                            <button type="button" onclick="resetImage()" class="btn btn-warning btn-sm mt-3">{{__('btnText.reset')}}</button>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">{{__('users.name')}}</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="fullName" value="{{$user->name}}">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="about" class="col-md-4 col-lg-3 col-form-label">{{__('nav.abt')}}</label>
                                        <div class="col-md-8 col-lg-9">
                                            <textarea name="about" class="form-control @error('about') is-invalid @enderror" id="about" style="height: 100px" placeholder="I like coding.">{{$user->about}}</textarea>
                                            @error('about')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="company" class="col-md-4 col-lg-3 col-form-label">{{__('users.dob')}}</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="birthdate" type="date" class="form-control" id="company" value="{{$user->birthdate}}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Phone" class="col-md-4 col-lg-3 col-form-label">{{__('users.ph')}}</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" id="Phone" value="{{$user->phone}}">
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">{{__('btnText.update')}}</button>
                                    </div>
                                </form><!-- End Profile Edit Form -->

                            </div>

                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form action="{{route(is_active_route_val('user.profile','user.password.change','user.dtl.pf_update'))}}" method="post">
                                    @csrf
                                    @method('POST')
                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="currentPassword" type="password" class="form-control @error('currentPassword') is-invalid @enderror" id="currentPassword" required>
                                            @error('newPassword')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newPassword" type="password" class="form-control @error('newPassword') is-invalid @enderror" id="newPassword" required>
                                            @error('newPassword')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewPassword" type="password" class="form-control @error('renewPassword') is-invalid @enderror" id="renewPassword" required>
                                            @error('renewPassword')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form><!-- End Change Password Form -->


                            </div>

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function imagePreview(event) {
            event.preventDefault();
            const preview = document.getElementById('image-preview');
            const file = event.target.files[0];
            const reader = new FileReader();
            preview.style.maxWidth = '150px';
            preview.style.maxHeight = '150px';
            preview.style.marginTop = '20px';

            reader.onload = () => {
                const img = new Image();
                img.src = reader.result;
                img.style.maxWidth = "100%";
                img.style.maxHeight = "100%";

                preview.innerHTML = '';
                preview.appendChild(img);
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function resetImage() {
            const input = document.getElementById('image-input');
            input.value = '';
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
        }


    </script>
@endsection

