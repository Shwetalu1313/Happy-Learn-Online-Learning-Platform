@extends('layouts.app')

@section('content')

    @php
    use App\Models\Course;
    use App\Models\User;
    use App\Enums\CourseStateEnums;

    $newCourses = Course::getNewCourseLimitSix();
    $students = User::students();
    $titlePage = 'home';
 @endphp

    <section class="container-fluid w-100 h-100 py-5 mb-3">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <div class="hero-text text-center">
                <h1 class="fw-bolder mb-4 gradient-text">Unlock Your Potential. Start Learning Today.</h1>

            </div>
            <img src="{{ asset('./storage/webstyle/owl_learn.webp') }}" class="img-fluid mt-4 owl-center" style="width: 20rem;" alt="Owl Learning Image">
            <div class="container text-center">
                <p class="mb-5 fs-4 border-animate">Unlock your potential with curated learning experiences. Start today!</p><br>
                <a href="#" class="button button--hoo d-inline-block">
                    <div class="button__wrapper">
                        <span class="button__text">START</span>
                    </div>
                    <div class="characterBox">
                        <div class="character wakeup">
                            <div class="character__face"></div>
                            <div class="charactor__face2"></div>
                            <div class="charactor__body"></div>
                        </div>
                        <div class="character wakeup">
                            <div class="character__face"></div>
                            <div class="charactor__face2"></div>
                            <div class="charactor__body"></div>
                        </div>
                        <div class="character">
                            <div class="character__face"></div>
                            <div class="charactor__face2"></div>
                            <div class="charactor__body"></div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </section>
    {{--end hero section--}}

    <section class="container-fluid h-100 p-5">
        <h2 class="text-center fw-bold text-forth mb-3">{{__('course.label_new')}}</h2>

        <div class="slides new-courses my-unselectable-element">
            @foreach($newCourses as $newCourse)
                <div class="card">
                    <div class="card-header d-block strong-card-header-gradient">
                        <h5 class="card-title fw-bolder myHover cursor-pointer text-center pt-3" onclick="window.location.href='{{ route('course.enroll',[$newCourse->id]) }}'">{{$newCourse->title}} @if($newCourse->state == CourseStateEnums::PENDING->value)
                                <span class="badge text-warning border border-warning"> beta </span>
                            @endif</h5>
                        <div class="float-end">

                                <p class="">{{$newCourse->created_at->diffForHumans()}}</p>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-around" title="{{__('course.label_name').' = '. $newCourse->title}}">
                        <p>{{$newCourse->lessons->count()}} - lessons</p>
                        <details>
                            <summary class="myHover"><i class="bi bi-person-video3 me-2"></i> Teachers</summary>
                            <ol>
                                @if($newCourse->creator->role->value != \App\Enums\UserRoleEnums::ADMIN->value)
                                    <li><a href="#">{{$newCourse->creator->name}}</a></li>
                                @endif
                                @foreach($newCourse->contribute_courses as $course)
                                    <li><a href="#"> {{$course->user->name}}</a></li>
                                @endforeach
                            </ol>
                        </details>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    {{--end new course section--}}

    <section class="container-fluid h-100 p-5">
        <h2 class="text-center fw-bold text-forth mb-3">{{__('course.label_popular')}}</h2>

        <div class="slides new-courses my-unselectable-element">
            @foreach($newCourses as $newCourse)
                <div class="card">
                    <div class="card-header d-block strong-card-header-gradient-success">
                        <h5 class="card-title fw-bolder myHover cursor-pointer text-center pt-3" onclick="window.location.href='{{ route('course.enroll',[$newCourse->id]) }}'">{{$newCourse->title}} @if($newCourse->state == CourseStateEnums::PENDING->value)
                                <span class="badge text-warning border border-warning"> beta </span>
                            @endif</h5>
                        <div class="float-end">

                            <p class="">{{$newCourse->created_at->diffForHumans()}}</p>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-around" title="{{__('course.label_name').' = '. $newCourse->title}}">
                        <p>{{$newCourse->lessons->count()}} - lessons</p>
                        <details>
                            <summary class="myHover"><i class="bi bi-person-video3 me-2"></i> Teachers</summary>
                            <ol>
                                @if($newCourse->creator->role->value != \App\Enums\UserRoleEnums::ADMIN->value)
                                    <li><a href="#">{{$newCourse->creator->name}}</a></li>
                                @endif
                                @foreach($newCourse->contribute_courses as $course)
                                    <li><a href="#"> {{$course->user->name}}</a></li>
                                @endforeach
                            </ol>
                        </details>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    {{--end popular course section--}}
    {{--for this course I will retirve the most enrolled course--}}

    <section class="container-fluid w-100 h-100 py-5 mb-3">
        <h1 class="fw-bold text-center mb-5">Community</h1>
        <div class="row">
            <div class="col-md-6 d-flex justify-content-around align-items-center">
                <div class="engagement-left">
                    <h3 class="text-center engagement-left-title">Join Our Thriving Community <span class="text-info">Today!</span></h3><br>
                    <p>Become a part of our dynamic community and seize the opportunity to excel! With a total student count reaching new heights, now is the perfect time to join us. Don't let the fear of missing out hold you back—embrace the chance to grow, learn, and succeed alongside your peers. Your journey towards success starts here. Enroll now and be part of something extraordinary!</p>

                    <div class="text-md-center">
                        <button class="button-design-primary" onclick="window.location.href='{{ route('users.top_pts') }}'"><i class="bi bi-people"></i> Total Students - {{$students->count()}}</button>
                        <button class="button-design-success ms-3 margin-small-top"><i class="bi bi-person-fill-add"></i> Join Here</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-around align-items-center">
                <img src="{{ asset('./storage/webstyle/owl_pupils.jpg') }}" class="img-fluid mt-4 owl-right rounded-5" style="width: 20rem;" alt="Owl Learning Image">
            </div>
        </div>
    </section>
    {{--end community section--}}

    <section class="container-fluid w-100 h-100 py-5 mb-3">
        {{--<h1 class="fw-bold text-center mb-5">Community</h1>--}}
        <div class="row">
            <div class="col-md-6 d-flex justify-content-around align-items-center">
                <img src="{{ asset('./storage/webstyle/owl_teacher.webp') }}" class="img-fluid mt-4 owl-right rounded-5" style="width: 20rem;" alt="Owl Learning Image">
            </div>

            <div class="col-md-6 d-flex justify-content-around align-items-center">
                <div class="engagement-right">
                    <h3 class="text-center engagement-right-title fs-2">Who will <span class="text-info">teach</span> you? </h3><br>
                    <p class="fs-5">Teachers from our community has relevant skills and certified diploma in their fields. We only offer who pass our interview hard questions. So, don't worry learners. We care about you.</p>

                    <div class="text-md-center">
                        <button class="button-design-primary" onclick="window.location.href='{{ route('users.teachers') }}'">
                            <i class="bi bi-person-video3 me-2"></i> Teachers
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </section>
    {{--end teacher section--}}


@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paragraph = document.querySelector(".border-animate");
            const slideContainer = document.querySelector('.slides');
            const slides = Array.from(slideContainer.children);
            const slideWidth =  slides[0].getBoundingClientRect().width;

            function timeSlideContainer(){
                setTimeout(()=> {
                    slideContainer.style.transition = 'transform 0.5s ease-in-out';
                    slideContainer.style.transform = 'translateX(0)';
                },0);
            }

            function showNextSlides(){
                const firstItem = slides.shift();
                slides.push(firstItem);
                slideContainer.style.transition = 'none';
                slideContainer.style.transform = `translateX(-${slideWidth})`;
                timeSlideContainer();
            }

            function showPreviousSlides(){
                const lastItem = slides.pop();
                slides.unshift(lastItem);
                slideContainer.style.transition = 'none';
                slideContainer.style.transform = `translateX(+${slideWidth})`;
                timeSlideContainer();
            }

            function handleScroll(event){
                event.preventDefault();
                const deltaY = event.deltaY || event.detail || event.wheelDelta;
                deltaY > 0 ? showNextSlides() : showPreviousSlides();
            }

            window.addEventListener('wheel', handleScroll);

            showNextSlides();
            setInterval(showNextSlides, 3000); //auto change next slide at every 3 seconds

            paragraph.addEventListener("mouseover", function() {
                const randomBorderRadius = Math.floor(Math.random() * 50) + 1; // Random number between 1 and 50
                paragraph.style.animation = `changeBorderRadius 1s ease`;
                paragraph.style.borderRadius = `${randomBorderRadius}%`;
            });
        });

    </script>
@endsection
