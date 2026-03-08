<footer class="hl-footer pt-5">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4">
                <h5 class="hl-footer-title mb-3">{{ config('app.name', 'Happy Learn') }}</h5>
                <p class="hl-footer-text mb-3">{{ __('footer.about_us_content') }}</p>
                <div class="d-flex gap-2">
                    <a href="#" class="hl-social" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="hl-social" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="hl-social" title="Telegram"><i class="bi bi-telegram"></i></a>
                    <a href="#" class="hl-social" title="Github"><i class="bi bi-github"></i></a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <h6 class="hl-footer-title mb-3">{{ __('footer.quick_links') }}</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    <li><a href="{{ route('home') }}" class="hl-footer-link">{{ __('nav.home') }}</a></li>
                    <li><a href="{{ route('course.list.learners') }}" class="hl-footer-link">{{ __('course.title') }}</a></li>
                    <li><a href="{{ route('job.intro') }}" class="hl-footer-link">{{ __('nav.opportunities') }}</a></li>
                    <li><a href="{{ route('users.teachers') }}" class="hl-footer-link">{{ __('nav.teacher_lst') }}</a></li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-4">
                <h6 class="hl-footer-title mb-3">{{ __('footer.contact_us') }}</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    <li class="hl-footer-text"><i class="bi bi-geo-alt me-2"></i>{{ __('footer.Address') }}</li>
                    <li class="hl-footer-text"><i class="bi bi-envelope-at me-2"></i>{{ __('footer.mail') }}</li>
                    <li class="hl-footer-text"><i class="bi bi-telephone me-2"></i>{{ __('footer.phone') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="hl-footer-bottom py-3">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>&copy; {{ Date('Y') }} {{ config('app.name', 'Happy Learn') }}. All rights reserved.</div>
            <div class="small">Built for modern learning experience.</div>
        </div>
    </div>
</footer>
