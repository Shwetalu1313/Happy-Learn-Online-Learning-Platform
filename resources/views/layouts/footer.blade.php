@section('styles')

@endsection

<footer class="bg--second ary pt-5 pb-1 mt-5">
    <div class="container pb-2">
        <div class="row">

            {{-- social media icon --}}
            <div class="text-center" style="height: 100%">
                <div class="glitch-wrapper mb-3">
                    <div class="glitch" data-text="Follow Us">Follow Us</div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="row gx-5 socialmedia">
                        <i class="bi bi-facebook fs-2 col cur-poi" title="Facebook"></i>
                        <i class="bi bi-twitter-x fs-2 col cur-poi" title="Twitter-X"></i>
                        <i class="bi bi-telegram fs-2 col cur-poi" title="Telegram"></i>
                        <i class="bi bi-github fs-2 col cur-poi" title="Github"></i>
                        <i class="bi bi-google fs-2 col cur-poi" title="Google"></i>
                    </div>
                </div>
            </div>
            {{-- end social media icon --}}
        </div>
        <hr class="custom-hr">
        <div class="row gx-3">
            <div class="px-4 col-lg-4 col-12 mb-lg-1 mb-3 border-end border-primary">
                <h2 class="text-third fs-bold mb-3">{{ __('footer.about_us') }}</h2>
                <p class="text-start fs-5">{{ __('footer.about_us_content') }}</p>

            </div>

            <div class="px-4 col-lg-4 col-12 mb-lg-1 mb-3 border-end border-primary">
                <h2 class="text-third fs-bold mb-3">{{ __('footer.quick_links') }}</h2>
                <ul class="list-unstyled border-primary">
                    <li><a href="{{route('job.intro')}}" class="fs-5">Home</a></li>
                    <li><a href="" class="fs-5">Courses</a></li>
                    <li><a href="" class="fs-5 {{is_active_route(['job.intro','job.listV2','job.detail'])}}">{{ __('nav.opportunities') }}</a></li>
                </ul>
            </div>

            <div class="px-4 col-lg-4 col-12 mb-lg-1 mb-3">
                <h2 class="fw-normal text-third fs-light mb-3">{{ __('footer.contact_us') }}</h2>
                <ul class="list-unstyled border-primary">
                    <li class="mb-2"><span class="me-3"><strong class="me-2"><i
                                    class="bi bi-building-gear fs-4"></i></strong></span>{{ __('footer.Address') }}
                    </li>
                    <li class="mb-2"><span class="me-3"><strong class="me-2"><i
                                    class="bi bi-envelope-at-fill fs-4"></i></strong></span>{{ __('footer.mail') }}
                    </li>
                    <li class="mb-2"><span class="me-3"><strong class="me-2"><i
                                    class="bi bi-telephone-fill fs-4"></i></strong></span>{{ __('footer.phone') }}
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="row bg--third d-block" style="margin: auto auto; max-height: 2rem;">
        <div class="col text-center">
            <p class="text-second">&copy; {{ Date('Y') }} <strong><span class="text-forth">Khant Nyein
                                Naing</span></strong>. All rights
                reserved.</p>
        </div>
    </div>

</footer>
