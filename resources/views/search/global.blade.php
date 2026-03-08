@extends($layout)

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-body pt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="card-title mb-0">Global Search</h5>
                    <small class="text-muted">Simple, indexed search across key platform data</small>
                </div>

                <form action="{{ route('global.search') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            value="{{ $query }}"
                            placeholder="Search courses, categories, subcategories, jobs{{ $isAdminSearchView ? ', users' : '' }}..."
                            minlength="2"
                        >
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>

                @if($query === '')
                    <div class="alert alert-info py-2 mb-0">Enter a keyword to start global search.</div>
                @elseif(mb_strlen($query) < 2)
                    <div class="alert alert-warning py-2 mb-0">Please enter at least 2 characters.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">
                            Result for: <strong>{{ $query }}</strong>
                        </div>
                        <div class="badge bg-secondary">{{ $resultTotal }} item(s)</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="mb-2">Courses ({{ $results['courses']->count() }})</h6>
                                @if($results['courses']->isEmpty())
                                    <div class="text-muted">No courses found.</div>
                                @else
                                    <ul class="mb-0">
                                        @foreach($results['courses'] as $course)
                                            <li class="mb-1">
                                                <a href="{{ $isAdminSearchView ? route('course.edit', $course->id) : route('course.enroll', $course->id) }}">
                                                    {{ $course->title }}
                                                </a>
                                                <small class="text-muted">
                                                    | {{ $course->sub_category?->name ?? 'Uncategorized' }}
                                                    @if($course->sub_category?->category)
                                                        / {{ $course->sub_category->category->name }}
                                                    @endif
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2">Categories ({{ $results['categories']->count() }})</h6>
                                @if($results['categories']->isEmpty())
                                    <div class="text-muted">No categories found.</div>
                                @else
                                    <ul class="mb-0">
                                        @foreach($results['categories'] as $category)
                                            <li class="mb-1">
                                                @if($isAdminSearchView)
                                                    <a href="{{ route('category.modify', $category->id) }}">{{ $category->name }}</a>
                                                @else
                                                    {{ $category->name }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2">Subcategories ({{ $results['subCategories']->count() }})</h6>
                                @if($results['subCategories']->isEmpty())
                                    <div class="text-muted">No subcategories found.</div>
                                @else
                                    <ul class="mb-0">
                                        @foreach($results['subCategories'] as $subCategory)
                                            <li class="mb-1">
                                                @if($isAdminSearchView)
                                                    <a href="{{ route('sub_category.edit', $subCategory->id) }}">{{ $subCategory->name }}</a>
                                                @else
                                                    {{ $subCategory->name }}
                                                @endif
                                                <small class="text-muted">
                                                    | {{ $subCategory->category?->name ?? 'No category' }}
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2">Jobs ({{ $results['jobs']->count() }})</h6>
                                @if($results['jobs']->isEmpty())
                                    <div class="text-muted">No jobs found.</div>
                                @else
                                    <ul class="mb-0">
                                        @foreach($results['jobs'] as $job)
                                            <li class="mb-1">
                                                <a href="{{ $isAdminSearchView ? route('job.show', $job->id) : route('job.detail', $job->id) }}">
                                                    {{ $job->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        @if($isAdminSearchView)
                            <div class="col-lg-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-2">Users ({{ $results['users']->count() }})</h6>
                                    @if($results['users']->isEmpty())
                                        <div class="text-muted">No users found.</div>
                                    @else
                                        <ul class="mb-0">
                                            @foreach($results['users'] as $user)
                                                <li class="mb-1">
                                                    <a href="{{ route('user.dtl.show', $user->id) }}">{{ $user->name }}</a>
                                                    <small class="text-muted">| {{ $user->email }}</small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
