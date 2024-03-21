@extends('layouts.app')
@section('content')
    <div class="container py-5">
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

                <div>
                    <h5 class="text-secondary-emphasis">Description:</h5>
                    <p>{{ $course->description }}</p>
                </div>

            </div>
            <div class="col-md-4 enroll-section">
                {{--TODO:: add Card Payment [visa, master, ...] form design--}}
                {{--<h2 class="text-center">{{__('btnText.enroll')}}</h2>--}}
                <div class="card border border-primary-subtle border-2 border-opacity-25">
                    <div class="card-body">
                        <h5 class="card-title text-secondary-emphasis mb-3">Pay with Points</h5>
                            <button type="submit" class="btn btn-warning mb-3">Pay Now</button>
                        <h5 class="card-title text-secondary-emphasis mb-3">Payment Information</h5>
                        <form>
                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="cardNumber" placeholder="Enter card number">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiryDate" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="CVV">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cardHolderName" class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" id="cardHolderName" placeholder="Enter cardholder name">
                            </div>
                            <div class="d-grid gap-2 col-6 mx-auto">
                                <button type="submit" class="btn btn-primary">Pay Now</button>
                            </div>
                            <div id="cardTypeIcons" class="mt-3"><i class="fa-brands fa-cc-visa fs-3 text-primary-emphasis"></i> <i class="fa-brands fa-cc-mastercard fs-3 text-danger-emphasis"></i></div>
                            <small class="text-secondary"> <span class="text-secondary-emphasis">Visa</span> and <span class="text-secondary-emphasis">Master</span> Card Payment are <span class="text-success-emphasis">available</span>.</small>
                        </form>
                    </div>
                </div>

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
                </script>



            </div>
        </div>
        {{--TODO:: add random related course with card view--}}
    </div>
@endsection
