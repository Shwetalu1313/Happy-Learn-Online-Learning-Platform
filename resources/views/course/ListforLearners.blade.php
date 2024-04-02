@extends('layouts.app')
@section('content')
    @php
        use App\Models\Category;
        use App\Enums\CourseStateEnums;
        use App\Models\Course;
        use App\Models\CourseEnrollUser;
        use App\Models\User;
        use App\Models\CurrencyExchange;
        use App\Enums\CourseTypeEnums;

        $us_ex = CurrencyExchange::getUSD();
        $basicCourseEnum = CourseTypeEnums::BASIC->value;
        $newCourses = Course::getNewCourseLimitSix();
        $popularCourses = CourseEnrollUser::PopularCourses();
        $students = User::students();
        $categories = Category::all();
    @endphp

    <div class="container py-5">
        <h1 class="text-center mb-3">Courses</h1>

        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="courseFilter" placeholder="Filter by title...">
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="freeCourseFilter">
                        <label class="form-check-label" for="freeCourseFilter">
                            Free Courses Only
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
        </div>

        <div class="container p-5 border rounded border-primary-subtle">
            @foreach($categories as $category)
                @if($category->sub_categories->count() != 0)
                    <div class="row">
                        <p class="fs-4 text-info" title="category name">{{$category->name}}</p>

                        @foreach($category->sub_categories as $j => $sub_category)
                            @if($sub_category->courses->count() != 0)
                                <div class="row mb-4">
                                    <p class="fs-5 text-secondary-emphasis border-bottom" title="sub-category name">{{$j+1}}. {{$sub_category->name}}</p>

                                    @foreach($sub_category->courses as $index => $course)
                                        <div class="col-md-4" style="display: block;">
                                            <div class="card">
                                                <div class="card-header d-block strong-card-header-gradient">
                                                    <h5 class="card-title fw-bolder myHover cursor-pointer text-center pt-3" onclick="window.location.href='{{ route('course.enroll',[$course->id]) }}'">{{$course->title}} @if($course->state == CourseStateEnums::PENDING->value)
                                                            <span class="badge text-warning border border-warning"> beta </span>
                                                        @endif</h5>
                                                    <div class="text-end">
                                                        <p class="">{{$course->created_at->diffForHumans()}}</p>
                                                    </div>
                                                </div>
                                                <div class="card-body" title="{{__('course.label_name').' = '. $course->title}}">
                                                    @if($course->courseType === $basicCourseEnum)
                                                        <p class="text-success-emphasis">This is free course.</p>
                                                    @else
                                                        <div class="d-flex justify-content-around mb-3">
                                                            <p class="fw-bold text-secondary">Price $:</p>
                                                            <div class="d-flex align-items-baseline">
                                                                <p class="fs-5 text-secondary">{{$course->fees}}</p>
                                                                <p class="fs-6 text-secondary-emphasis ms-2">{{__('nav.mmk')}}</p>
                                                            </div>
                                                            <i class="bi bi-arrow-left-right mx-3 text-secondary-emphasis"></i>
                                                            <div class="d-flex align-items-baseline">
                                                                <p class="fs-5 text-secondary">{{MoneyExchange($course->fees,$us_ex)}}</p>
                                                                <p class="fs-6 text-secondary-emphasis ms-2">{{__('nav.us_dol')}}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <hr>
                                                    <div class="d-flex justify-content-around">
                                                        <p class="course-index text-secondary">No. {{$index + 1}}</p> <!-- Display course index -->
                                                        <p>{{$course->lessons->count()}} - lessons</p>
                                                        <details>
                                                            <summary class="myHover"><i class="bi bi-person-video3 me-2"></i> Teachers</summary>
                                                            <ol>
                                                                @if($course->creator->role->value != \App\Enums\UserRoleEnums::ADMIN->value)
                                                                    <li><a href="#">{{$course->creator->name}}</a></li>
                                                                @endif
                                                                @foreach($course->contribute_courses as $course)
                                                                    <li><a href="#"> {{$course->user->name}}</a></li>
                                                                @endforeach
                                                            </ol>
                                                        </details>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endif
                        @endforeach
                    </div>
                    <hr class="text-info my-5">
                @endif
            @endforeach
        </div>
    </div>


    <script>
        function applyFilters() {
            var filterInput = document.getElementById('courseFilter').value.toLowerCase();
            var freeOnly = document.getElementById('freeCourseFilter').checked;

            var cards = document.getElementsByClassName('card');
            var visibleIndex = 0; // Index to keep track of visible courses

            for (var i = 0; i < cards.length; i++) {
                var card = cards[i];
                var title = card.querySelector('.card-title').textContent.toLowerCase();
                var isFree = card.querySelector('.text-success-emphasis');

                var shouldDisplay = title.includes(filterInput);

                if (freeOnly && !isFree) {
                    shouldDisplay = false;
                }

                if (shouldDisplay) {
                    card.style.display = 'block';
                    visibleIndex++;
                } else {
                    card.style.display = 'none';
                }

                // Update the index text for the visible courses
                if (shouldDisplay) {
                    card.querySelector('.course-index').textContent = "No. " + visibleIndex;
                }
            }
        }
    </script>


@endsection
