@extends('layouts.app')
@section('content')
    @php
        use App\Enums\CourseTypeEnums;
        use App\Models\CurrencyExchange;
        $basicCourseEnum = CourseTypeEnums::BASIC->value;
        $us_ex = CurrencyExchange::getUSD();
        $pts_ex = CurrencyExchange::getPts();
    @endphp
    <div class="container py-5">

        {{--alert--}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{$error}}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-bag-x me-3"></i> {{session('error')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2-circle text-success me-3"></i> {{session('success')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{--end alert--}}

        <div class="header">
            <h3 class="text-info fw-bold enroll-course-title" title="course name">{{$course->title}}</h3>
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" title="category"><a href="#">{{$course->sub_category->category->name}}</a></li>
                    <li class="breadcrumb-item" title="sub_category" aria-current="page">{{$course->sub_category->name}}</li>
                    <li class="breadcrumb-item active" title="course name" aria-current="page">{{$course->title}}</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-8 p-5">

                <img src="{{ asset('storage/'.$course->image) }}" class="text-center object-fit-cover w-100 h-50 mx-auto mb-5" alt="Course Image">
                <p class="text-secondary fs-4">Total Lessons: <span class="text-forth">{{ $course->lessons->count() }}</span> </p>
                <div class="d-flex align-items-baseline" title="course Fee">
                    <i class="bi bi-cash-coin me-4 fs-5 text-primary-emphasis"></i>
                    <p class="fw-bold fs-4 me-2">{{ $course->fees }}</p>
                    <p class="text-secondary fs-5">{{__('nav.mmk')}}</p>
                </div>

                <div class="">
                    <h5 class="text-secondary-emphasis">Description:</h5>
                    <p class="">{{ $course->description }}</p>
                </div>

            </div>
            {{--end left side--}}

            <div class="col-md-4 enroll-section">
                <div class="card border border-secondary-subtle border-2 border-opacity-25">
                    <div class="card-body">
                        @if($course->courseType === $basicCourseEnum)
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <small class="text-primary-emphasis fs-5">This is basic course. So, you can get it free.</small> <br>
                                <button type="submit" class="btn btn-success my-5" onclick="document.getElementById('freePay').submit();">Enroll Free</button>

                                <form action="{{route('course.freePayment')}}" method="POST" class="d-none" id="freePay">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" value="{{$course->id}}" name="course_id">
                                    <input type="hidden" value="{{$course->fees}}" name="amount">
                                </form>
                            </div>

                        @else
                            <h5 class="card-title text-secondary-emphasis mb-3">Pay with Points</h5>
                            <div class="d-flex align-items-baseline justify-content-center">
                                <p class="fs-3 fw-lighter">{{$course->fees}}</p>
                                <p class="ms-2 text-secondary-emphasis">{{__('nav.mmk')}}</p>
                                <i class="bi bi-arrow-left-right mx-3 text-secondary-emphasis"></i>
                                <p class="fs-3 fw-lighter">{{MoneyExchange($course->fees,$pts_ex)}}</p>
                                <p class="mx-2 text-secondary-emphasis">{{__('nav.pts')}}</p>

                                <form action="{{route('course.ptsPayment')}}" method="POST" class="d-none" id="ptsPay">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" value="{{MoneyExchange($course->fees,$pts_ex)}}" name="pts">
                                    <input type="hidden" value="{{$course->id}}" name="course_id">
                                    <input type="hidden" value="{{$course->fees}}" name="amount">
                                </form>
                            </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-warning mb-3" onclick="document.getElementById('ptsPay').submit();">Pay Now</button>
                        </div>
                        <hr>
                            <h5 class="card-title text-secondary-emphasis mb-3">Payment Information</h5>
                            <div class="d-flex align-items-baseline justify-content-center">
                                <p class="fs-4 fw-lighter">{{$course->fees}}</p>
                                <p class="ms-3 text-secondary-emphasis">{{__('nav.mmk')}}</p>
                                <i class="bi bi-arrow-left-right mx-3 text-secondary-emphasis"></i>
                                <p class="fs-4 fw-lighter">{{MoneyExchange($course->fees,$us_ex)}}</p>
                                <p class="mx-3 text-secondary-emphasis">{{__('nav.us_dol')}}</p>
                            </div>
                            <form id="cardPay" action="{{route('course.cardPayment')}}" method="POST" onsubmit="return validateForm()">
                                @csrf
                                @method('POST')
                                <input type="hidden" value="{{$course->id}}" name="course_id">
                                <input type="hidden" value="{{$course->fees}}" name="amount">
                                <div class="mb-3">
                                    <label for="cardNumber" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="Enter card number" required pattern="[0-9]{16}" title="Please enter a valid 16-digit card number">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiryDate" name="expired_date" placeholder="MM/YY" required pattern="(0[1-9]|1[0-2])\/[0-9]{2}" title="Please enter a valid expiry date (MM/YY)">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="CVV" required pattern="[0-9]{3}" title="Please enter a valid 3-digit CVV">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="cardHolderName" class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" id="cardHolderName" name="cardHolderName" placeholder="Enter cardholder name" required>
                                </div>
                                <div class="d-grid gap-2 col-6 mx-auto">
                                    <button type="submit" class="btn btn-primary">Pay Now</button>
                                </div>
                                <div id="cardTypeIcons" class="mt-3"><i class="fa-brands fa-cc-visa fs-3 text-primary-emphasis"></i> <i class="fa-brands fa-cc-mastercard fs-3 text-danger-emphasis"></i></div>
                                <small class="text-secondary"> <span class="text-secondary-emphasis">Visa</span> and <span class="text-secondary-emphasis">Master</span> Card Payment are <span class="text-success-emphasis">available</span>.</small>
                            </form>
                        @endif

                    </div>
                </div>
                {{--end right side--}}

                <script>
                    document.getElementById('cardNumber').addEventListener('input', function() {
                        let cardNumber = this.value.replace(/\s/g, '');
                        let cardTypeIcons = document.getElementById('cardTypeIcons');
                        let visaIcon = '<i class="fa-brands fa-cc-visa fs-3 text-primary-emphasis"></i>';
                        let mastercardIcon = '<i class="fa-brands fa-cc-mastercard fs-3 text-danger-emphasis"></i>';

                        if (/^4/.test(cardNumber)) {
                            cardTypeIcons.innerHTML = visaIcon;
                        } else if (/^5[1-5]/.test(cardNumber)) {
                            cardTypeIcons.innerHTML = mastercardIcon;
                        } else {
                            cardTypeIcons.innerHTML = visaIcon +' '+ mastercardIcon;
                        }
                    });

                    function validateForm() {
                        const cardNumber = document.getElementById('cardNumber').value;
                        const expiryDate = document.getElementById('expiryDate').value;
                        const cvv = document.getElementById('cvv').value;
                        const cardHolderName = document.getElementById('cardHolderName').value;

                        // Check if card number is valid
                        if (!cardNumber.match(/[0-9]{16}/)) {
                            alert('Please enter a valid 16-digit card number');
                            return false;
                        }

                        // Check if expiry date is valid
                        if (!expiryDate.match(/(0[1-9]|1[0-2])\/[0-9]{2}/)) {
                            alert('Please enter a valid expiry date (MM/YY)');
                            return false;
                        }

                        // Check if CVV is valid
                        if (!cvv.match(/[0-9]{3}/)) {
                            alert('Please enter a valid 3-digit CVV');
                            return false;
                        }

                        // Check if cardholder name is not empty
                        if (cardHolderName.trim() === '') {
                            alert('Please enter cardholder name');
                            return false;
                        }

                        // Form is valid, submit the form
                        return true;
                    }
                </script>



            </div>
        </div>
        {{--TODO:: add random related course with card view--}}
    </div>
@endsection
