@extends('layouts.app')

@section('content')
    @php
        use App\Enums\CourseTypeEnums;

        $enrolledCount = $enrollments->count();
        $freeCount = $enrollments->filter(fn ($item) => $item->course && $item->course->courseType === CourseTypeEnums::BASIC->value)->count();
        $paidCount = $enrolledCount - $freeCount;
        $spentMmk = (int) $enrollments->sum('amount');
        $latestEnrollment = $enrollments->first();
    @endphp

    <style>
        .student-premium {
            --surface: #0b1220;
            --card: #121a2b;
            --line: #263551;
            --ink: #e8eef9;
            --muted: #95a7c5;
            --accent: #4f8cff;
            --accent-2: #34b3ff;
            --success: #34d399;
            --danger: #f87171;
            font-family: "Nunito", "Poppins", sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, rgba(9, 14, 25, 0.86), rgba(8, 13, 22, 0.95));
            border: 1px solid #1d2941;
            border-radius: 18px;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.35);
        }

        .student-hero {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(130deg, #17243b 0%, #132137 58%, #101b2e 100%);
            padding: 1.2rem;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .student-hero::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            right: -140px;
            bottom: -180px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(79, 140, 255, 0.22), rgba(79, 140, 255, 0));
            pointer-events: none;
        }

        .student-hero h2 {
            margin: 0;
            font-weight: 800;
        }

        .student-hero p {
            margin: 0.4rem 0 0;
            color: var(--muted);
            max-width: 720px;
        }

        .student-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 0.95rem;
        }

        .student-actions .btn {
            border-radius: 10px;
            font-weight: 700;
            padding: 0.45rem 0.8rem;
        }

        .student-premium .btn-outline-secondary {
            border-color: #466089;
            color: #bfd0eb;
        }

        .student-premium .btn-outline-secondary:hover {
            background: #223557;
            border-color: #5f7eaf;
            color: #e6eef9;
        }

        .student-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(170px, 1fr));
            gap: 0.75rem;
            margin-top: 0.95rem;
        }

        .student-stat {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 0.8rem;
        }

        .student-stat .label {
            margin: 0;
            font-size: 0.78rem;
            color: var(--muted);
        }

        .student-stat .value {
            margin: 0.16rem 0 0;
            font-size: 1.42rem;
            font-weight: 800;
            color: var(--ink);
        }

        .student-stat .hint {
            margin: 0.2rem 0 0;
            font-size: 0.74rem;
            color: #7f93b1;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .student-course {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .student-course:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.38);
            border-color: #36507a;
        }

        .student-course img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            background: #0e1627;
        }

        .student-course .body {
            padding: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .student-course h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .student-meta {
            margin: 0;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .student-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
        }

        .student-badge {
            border: 1px solid #35507a;
            background: #0f1a2b;
            color: #bfd0eb;
            border-radius: 999px;
            padding: 0.18rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .student-badge.success {
            border-color: #2a6b53;
            background: #132a24;
            color: var(--success);
        }

        .student-badge.danger {
            border-color: #7a2f39;
            background: #2c121a;
            color: var(--danger);
        }

        .student-course .cta {
            display: flex;
            gap: 0.45rem;
            margin-top: auto;
            flex-wrap: wrap;
        }

        .student-course .cta .btn {
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.36rem 0.65rem;
        }

        .student-empty {
            margin-top: 1rem;
            border: 1px dashed #34527c;
            border-radius: 14px;
            padding: 1.3rem;
            background: #0f1a2b;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 1199px) {
            .student-stat-grid {
                grid-template-columns: repeat(2, minmax(170px, 1fr));
            }

            .student-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
        }

        @media (max-width: 767px) {
            .student-stat-grid,
            .student-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container py-4 student-premium">
        <section class="student-hero">
            <h2>Learning Workspace</h2>
            <p>
                Premium learner dashboard for tracking your course library and study flow.
                @if($latestEnrollment && $latestEnrollment->course)
                    Latest enrolled: <strong>{{ $latestEnrollment->course->title }}</strong>.
                @endif
            </p>
            <div class="student-actions">
                <a href="{{ route('course.list.learners') }}" class="btn btn-primary">Browse Courses</a>
                <a href="{{ route('users.top_pts') }}" class="btn btn-outline-primary">Leaderboard</a>
                <a href="{{ route('users.teachers') }}" class="btn btn-outline-secondary">Teachers</a>
            </div>

            <div class="student-stat-grid">
                <article class="student-stat">
                    <p class="label">Enrolled Courses</p>
                    <p class="value">{{ number_format($enrolledCount) }}</p>
                    <p class="hint">Total in your library</p>
                </article>
                <article class="student-stat">
                    <p class="label">Free Courses</p>
                    <p class="value">{{ number_format($freeCount) }}</p>
                    <p class="hint">Basic courses</p>
                </article>
                <article class="student-stat">
                    <p class="label">Paid Courses</p>
                    <p class="value">{{ number_format($paidCount) }}</p>
                    <p class="hint">Premium courses</p>
                </article>
                <article class="student-stat">
                    <p class="label">Total Spent</p>
                    <p class="value">{{ number_format($spentMmk) }} MMK</p>
                    <p class="hint">Enrollment payments</p>
                </article>
            </div>
        </section>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-bag-x me-2"></i>{{ Session::pull('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check2-circle me-2"></i>{{ Session::pull('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($enrollments->isEmpty())
            <section class="student-empty">
                <h5 class="mb-2">Your library is empty</h5>
                <p class="mb-3">Explore courses and enroll to start your learning journey.</p>
                <a href="{{ route('course.list.learners') }}" class="btn btn-primary">Explore Courses</a>
            </section>
        @else
            <section class="student-grid">
                @foreach($enrollments as $enrollment)
                    @php
                        $course = $enrollment->course;
                    @endphp

                    @if($course)
                        <article class="student-course">
                            <img src="{{ asset('storage/'.ltrim($course->image, '/')) }}" alt="{{ $course->title }}">
                            <div class="body">
                                <h6>{{ $course->title }}</h6>
                                <p class="student-meta">
                                    {{ $course->sub_category?->category?->name ?? 'General' }}
                                    @if($course->sub_category)
                                        / {{ $course->sub_category->name }}
                                    @endif
                                </p>
                                <div class="student-badges">
                                    <span class="student-badge {{ $course->courseType === CourseTypeEnums::BASIC->value ? 'success' : 'danger' }}">
                                        {{ $course->courseType === CourseTypeEnums::BASIC->value ? 'Free' : 'Paid' }}
                                    </span>
                                    <span class="student-badge">{{ $course->lessons_count }} lessons</span>
                                    <span class="student-badge">{{ $enrollment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="cta">
                                    <a href="{{ route('course.detail', $course->id) }}" class="btn btn-primary">Continue</a>
                                    <form action="{{ route('enroll.delete', $enrollment) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-outline-danger" onclick="return confirm('Do you really want to unsubscribe from this course: {{ addslashes($course->title) }}?')">
                                            Unsubscribe
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </section>
        @endif
    </div>
@endsection
