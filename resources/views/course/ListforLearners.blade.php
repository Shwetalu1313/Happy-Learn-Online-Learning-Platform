@extends('layouts.app')
@section('content')
    @php
        use App\Enums\CourseStateEnums;
    @endphp

    <style>
        .hl-course-shell {
            background: linear-gradient(165deg, rgba(13, 26, 49, 0.9), rgba(11, 34, 45, 0.88));
            border: 1px solid rgba(131, 167, 228, 0.24);
            border-radius: 22px;
            padding: 1.2rem;
            box-shadow: 0 22px 48px rgba(2, 8, 23, 0.44);
        }

        .hl-filter-box {
            background: rgba(5, 13, 25, 0.62);
            border: 1px solid rgba(133, 173, 245, 0.2);
            border-radius: 16px;
            padding: 0.9rem;
        }

        .hl-category-title {
            color: #c4dafe;
            font-weight: 700;
            border-left: 4px solid #22d3ee;
            padding-left: 0.7rem;
        }

        .hl-sub-category-title {
            color: #93c5fd;
            border-bottom: 1px solid rgba(147, 197, 253, 0.35);
            padding-bottom: 0.35rem;
        }

        .hl-course-card {
            background: linear-gradient(160deg, rgba(12, 24, 47, 0.95), rgba(10, 39, 48, 0.88));
            border: 1px solid rgba(112, 170, 236, 0.24);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.28);
        }

        .hl-course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 42px rgba(0, 0, 0, 0.38);
            border-color: rgba(99, 186, 255, 0.62);
        }

        .hl-course-thumb {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: rgba(2, 6, 17, 0.78);
        }

        .hl-course-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.01);
            transition: transform 0.35s ease;
        }

        .hl-course-card:hover .hl-course-thumb img {
            transform: scale(1.06);
        }

        .hl-course-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            border-radius: 999px;
            padding: 0.25rem 0.6rem;
            font-size: 0.74rem;
            font-weight: 700;
            backdrop-filter: blur(4px);
        }

        .hl-course-badge-free {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.5);
        }

        .hl-course-badge-pro {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.6);
        }

        .hl-course-body {
            padding: 0.9rem 0.95rem 1rem;
            color: #dbeafe;
        }

        .hl-course-title {
            font-size: 1rem;
            font-weight: 700;
            color: #f8fbff;
            line-height: 1.35;
            margin-bottom: 0.35rem;
            min-height: 2.7rem;
        }

        .hl-course-meta {
            color: #9db6df;
            font-size: 0.82rem;
            margin-bottom: 0.55rem;
        }

        .hl-price-grid {
            border: 1px solid rgba(135, 180, 250, 0.22);
            background: rgba(7, 19, 34, 0.56);
            border-radius: 12px;
            padding: 0.52rem 0.62rem;
            margin-bottom: 0.65rem;
        }

        .hl-course-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            color: #9ab4dd;
            font-size: 0.83rem;
        }
    </style>

    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="fw-bold text-light">Discover Courses</h1>
            <p class="text-secondary">Browse by category, compare pricing, and start learning in minutes.</p>
        </div>

        <div class="hl-filter-box mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="courseFilter" placeholder="Filter by title...">
                </div>
                <div class="col-lg-3">
                    <div class="form-check text-light">
                        <input class="form-check-input" type="checkbox" id="freeCourseFilter">
                        <label class="form-check-label" for="freeCourseFilter">
                            Free courses only
                        </label>
                    </div>
                </div>
                <div class="col-lg-3 d-grid d-lg-block">
                    <button class="btn btn-info text-dark fw-semibold px-4" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
        </div>

        <div class="hl-course-shell">
            @foreach($categories as $category)
                @if($category->sub_categories->isNotEmpty())
                    <div class="mb-4">
                        <p class="fs-4 hl-category-title">{{ $category->name }}</p>

                        @foreach($category->sub_categories as $j => $sub_category)
                            @if($sub_category->courses->isNotEmpty())
                                <div class="mb-4">
                                    <p class="fs-5 hl-sub-category-title">{{ $j + 1 }}. {{ $sub_category->name }}</p>
                                    <div class="row g-3">
                                        @foreach($sub_category->courses as $index => $course)
                                            @if($course->lessons_count > 0)
                                                @php
                                                    $courseImage = $course->image ? asset('storage/' . ltrim($course->image, '/')) : asset('assets/illustrations/course-placeholder.svg');
                                                    $isFree = $course->courseType === $basicCourseEnum;
                                                @endphp
                                                <div class="col-xl-4 col-md-6 hl-course-card-wrap"
                                                     data-course-title="{{ strtolower($course->title) }}"
                                                     data-is-free="{{ $isFree ? '1' : '0' }}">
                                                    <article class="hl-course-card">
                                                        <div class="hl-course-thumb cursor-pointer" onclick="window.location.href='{{ route('course.enroll', [$course->id]) }}'">
                                                            <img src="{{ $courseImage }}"
                                                                 alt="{{ $course->title }}"
                                                                 loading="lazy"
                                                                 decoding="async"
                                                                 onerror="this.onerror=null;this.src='{{ asset('assets/illustrations/course-placeholder.svg') }}';">
                                                            <span class="hl-course-badge {{ $isFree ? 'hl-course-badge-free' : 'hl-course-badge-pro' }}">
                                                                {{ $isFree ? 'FREE' : 'PREMIUM' }}
                                                            </span>
                                                        </div>
                                                        <div class="hl-course-body">
                                                            <h5 class="hl-course-title cursor-pointer" onclick="window.location.href='{{ route('course.enroll', [$course->id]) }}'">
                                                                {{ $course->title }}
                                                                @if($course->state == CourseStateEnums::PENDING->value)
                                                                    <span class="badge text-warning border border-warning ms-1">beta</span>
                                                                @endif
                                                            </h5>
                                                            <p class="hl-course-meta mb-2">{{ $course->created_at->diffForHumans() }} • {{ $course->lessons_count }} lessons</p>

                                                            @if($isFree)
                                                                <div class="hl-price-grid text-success-emphasis fw-semibold">No charge. Start instantly.</div>
                                                            @else
                                                                <div class="hl-price-grid">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="text-secondary">MMK</span>
                                                                        <span class="fw-semibold text-light">{{ number_format($course->fees) }}</span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="text-secondary">USD</span>
                                                                        <span class="fw-semibold text-light">{{ number_format(MoneyExchange($course->fees, $us_ex), 2) }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="hl-course-footer">
                                                                <span class="course-index">No. {{ $index + 1 }}</span>
                                                                <details>
                                                                    <summary class="text-info-emphasis"><i class="bi bi-person-video3 me-1"></i>Teachers</summary>
                                                                    <ol class="mb-0 mt-1">
                                                                        @if($course->creator->role->value != \App\Enums\UserRoleEnums::ADMIN->value)
                                                                            <li><a href="#" class="text-decoration-none text-light">{{ $course->creator->name }}</a></li>
                                                                        @endif
                                                                        @foreach($course->contribute_courses as $contributorCourse)
                                                                            <li><a href="#" class="text-decoration-none text-light">{{ $contributorCourse->user->name }}</a></li>
                                                                        @endforeach
                                                                    </ol>
                                                                </details>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <hr class="text-info my-4">
                @endif
            @endforeach
        </div>
    </div>

    <script>
        function applyFilters() {
            const filterInput = document.getElementById('courseFilter').value.trim().toLowerCase();
            const freeOnly = document.getElementById('freeCourseFilter').checked;
            const cards = document.querySelectorAll('.hl-course-card-wrap');
            let visibleIndex = 0;

            cards.forEach((cardWrap) => {
                const title = cardWrap.dataset.courseTitle || '';
                const isFree = cardWrap.dataset.isFree === '1';
                let shouldDisplay = title.includes(filterInput);

                if (freeOnly && !isFree) {
                    shouldDisplay = false;
                }

                cardWrap.style.display = shouldDisplay ? '' : 'none';
                if (shouldDisplay) {
                    visibleIndex++;
                    const indexEl = cardWrap.querySelector('.course-index');
                    if (indexEl) {
                        indexEl.textContent = 'No. ' + visibleIndex;
                    }
                }
            });
        }
    </script>
@endsection
