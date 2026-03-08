@extends('layouts.app')

@section('content')
    @php
        use App\Enums\CourseStateEnums;

        $approvedCount = $courses->where('state', CourseStateEnums::APPROVED->value)->count();
        $pendingCount = $courses->where('state', CourseStateEnums::PENDING->value)->count();
        $totalLessons = $courses->sum('lessons_count');
        $latestCourse = $courses->sortByDesc('created_at')->first();
    @endphp

    <style>
        .teacher-premium {
            --surface: #0b1220;
            --card: #121a2b;
            --line: #263551;
            --ink: #e8eef9;
            --muted: #95a7c5;
            --accent: #34b3ff;
            --accent-2: #4f8cff;
            --success: #34d399;
            --warning: #fb923c;
            font-family: "Nunito", "Poppins", sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, rgba(9, 14, 25, 0.86), rgba(8, 13, 22, 0.95));
            border: 1px solid #1d2941;
            border-radius: 18px;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.35);
        }

        .teacher-hero {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(130deg, #17243b 0%, #132137 58%, #101b2e 100%);
            padding: 1.2rem;
            overflow: hidden;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.3);
        }

        .teacher-hero::before {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            right: -130px;
            top: -180px;
            background: radial-gradient(circle, rgba(79, 140, 255, 0.24), rgba(79, 140, 255, 0));
            border-radius: 999px;
            pointer-events: none;
        }

        .teacher-hero h2 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.01em;
        }

        .teacher-hero p {
            margin: 0.4rem 0 0;
            color: var(--muted);
            max-width: 720px;
        }

        .teacher-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 1rem;
        }

        .teacher-actions .btn {
            border-radius: 10px;
            font-weight: 700;
            padding: 0.45rem 0.8rem;
        }

        .teacher-premium .btn-outline-secondary {
            border-color: #466089;
            color: #bfd0eb;
        }

        .teacher-premium .btn-outline-secondary:hover {
            background: #223557;
            border-color: #5f7eaf;
            color: #e6eef9;
        }

        .teacher-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(170px, 1fr));
            gap: 0.75rem;
            margin-top: 0.95rem;
        }

        .teacher-stat {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 0.8rem;
        }

        .teacher-stat .label {
            margin: 0;
            font-size: 0.78rem;
            color: var(--muted);
        }

        .teacher-stat .value {
            margin: 0.15rem 0 0;
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--ink);
        }

        .teacher-stat .hint {
            margin: 0.2rem 0 0;
            font-size: 0.74rem;
            color: #7f93b1;
        }

        .teacher-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .teacher-course {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .teacher-course:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.38);
            border-color: #36507a;
        }

        .teacher-course img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            background: #0e1627;
        }

        .teacher-course .body {
            padding: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            flex: 1;
        }

        .teacher-course h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--ink);
        }

        .teacher-meta {
            font-size: 0.78rem;
            color: var(--muted);
            margin: 0;
        }

        .teacher-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
        }

        .teacher-badge {
            border: 1px solid #35507a;
            background: #0f1a2b;
            color: #bfd0eb;
            border-radius: 999px;
            padding: 0.18rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .teacher-badge.success {
            border-color: #2a6b53;
            background: #132a24;
            color: var(--success);
        }

        .teacher-badge.warning {
            border-color: #7a4a26;
            background: #2a1a0e;
            color: var(--warning);
        }

        .teacher-course .cta {
            display: flex;
            gap: 0.45rem;
            margin-top: auto;
        }

        .teacher-course .cta .btn {
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.36rem 0.65rem;
        }

        .teacher-empty {
            margin-top: 1rem;
            border: 1px dashed #34527c;
            border-radius: 14px;
            padding: 1.3rem;
            background: #0f1a2b;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 1199px) {
            .teacher-stat-grid {
                grid-template-columns: repeat(2, minmax(170px, 1fr));
            }

            .teacher-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
        }

        @media (max-width: 767px) {
            .teacher-stat-grid,
            .teacher-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container py-4 teacher-premium">
        <section class="teacher-hero">
            <h2>Teaching Command Center</h2>
            <p>
                Focused workspace for course delivery and updates.
                @if($latestCourse)
                    Latest update: <strong>{{ $latestCourse->title }}</strong> ({{ $latestCourse->created_at->diffForHumans() }}).
                @endif
            </p>
            <div class="teacher-actions">
                <a href="{{ route('course.create') }}" class="btn btn-primary">Create Course</a>
                <a href="{{ route('course.index') }}" class="btn btn-outline-primary">Manage Courses</a>
                <a href="{{ route('lesson.index') }}" class="btn btn-outline-secondary">Lessons</a>
                <a href="{{ route('enroll.list') }}" class="btn btn-outline-secondary">Enrollments</a>
            </div>

            <div class="teacher-stat-grid">
                <article class="teacher-stat">
                    <p class="label">Total Courses</p>
                    <p class="value">{{ number_format($courses->count()) }}</p>
                    <p class="hint">All assigned/owned courses</p>
                </article>
                <article class="teacher-stat">
                    <p class="label">Approved</p>
                    <p class="value">{{ number_format($approvedCount) }}</p>
                    <p class="hint">Ready for learners</p>
                </article>
                <article class="teacher-stat">
                    <p class="label">Pending</p>
                    <p class="value">{{ number_format($pendingCount) }}</p>
                    <p class="hint">Awaiting approval</p>
                </article>
                <article class="teacher-stat">
                    <p class="label">Total Lessons</p>
                    <p class="value">{{ number_format($totalLessons) }}</p>
                    <p class="hint">Across all courses</p>
                </article>
            </div>
        </section>

        @if($courses->isEmpty())
            <section class="teacher-empty">
                <h5 class="mb-2">No courses yet</h5>
                <p class="mb-3">Create your first course to start building your teaching library.</p>
                <a href="{{ route('course.create') }}" class="btn btn-primary">Create First Course</a>
            </section>
        @else
            <section class="teacher-grid">
                @foreach($courses as $course)
                    <article class="teacher-course">
                        <img src="{{ $course->image ? asset('storage/' . ltrim($course->image, '/')) : asset('assets/illustrations/course-placeholder.svg') }}"
                             alt="{{ $course->title }}"
                             loading="lazy"
                             decoding="async"
                             onerror="this.onerror=null;this.src='{{ asset('assets/illustrations/course-placeholder.svg') }}';">
                        <div class="body">
                            <h6>{{ $course->title }}</h6>
                            <p class="teacher-meta">
                                {{ $course->sub_category?->category?->name ?? 'General' }}
                                @if($course->sub_category)
                                    / {{ $course->sub_category->name }}
                                @endif
                            </p>
                            <div class="teacher-badges">
                                <span class="teacher-badge {{ $course->state === CourseStateEnums::APPROVED->value ? 'success' : 'warning' }}">
                                    {{ $course->state === CourseStateEnums::APPROVED->value ? 'Approved' : 'Pending' }}
                                </span>
                                <span class="teacher-badge">{{ $course->lessons_count }} lessons</span>
                                <span class="teacher-badge">{{ $course->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="cta">
                                <a href="{{ route('course.edit', $course->id) }}" class="btn btn-outline-primary">Manage</a>
                                <a href="{{ route('lesson.createForm', $course->id) }}" class="btn btn-primary">Add Lesson</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
@endsection
