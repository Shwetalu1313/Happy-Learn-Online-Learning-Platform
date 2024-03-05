@extends('layouts.app')
@section('content')
    <div class="container mb-3 mb-sm-5">


        <section class="row mb-5 " style="height: 70svh">
            <h1 class="text-forth fs-bold jobintro-title">{{ __('nav.opportunities') }}</h1>
            <div class="col-lg-7 pb-lg-5 col-12 job-intro-text d-flex flex-column justify-content-center">
                <p class="mx-auto my-auto jobintromoti"><i class="bi bi-quote"></i>{{ __('jobapplication.motivation_text') }}
                </p><br>
                <p class="jobintroguide mb-3 text-third ">{{ __('jobapplication.guide') }}</p>
                <button type="button" class="btn btn-primary jobtxtguide"
                        style="max-width: 8rem; height: 3rem;" onclick="window.location.href='{{ route('job.listV2') }}'">{{ __('jobapplication.buttonApply') }}</button>

            </div>
            <div class="col-lg-5 col-12 jobimg-intro-column  overflow-hidden"
                style="height: 100%; margin-left:0; margin-top:0;">
                <img src="{{ '/storage/webstyle/wol.webp' }}" class="image-fluid jobOwl"
                    alt="{{ __('jobapplication.owl') }}">
            </div>
        </section>
        <hr>

        <section class="container py-5" style="height: 100%">
            <h2 class="text-center fs-bold text-forth mb-5">{{ __('jobapplication.benefits') }}</h2>
            {{-- <div class="container text-center"> --}}
            <div class="row g-5">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card">
                        <img src="/storage/webstyle/happy_tiger_money.webp" class="card-img-top" alt="...">
                        <div class="card-body">
                            <p class="card-title text-info" style="text-decoration-line: underline;">
                                {{ __('jobapplication.salary') }}</p>
                            <p class="card-text">{{ __('jobapplication.salary_text') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card">
                        <img src="/storage/webstyle/dopphin_work.webp" class="card-img-top" alt="...">
                        <div class="card-body">
                            <p class="card-title text-info" style="text-decoration-line: underline;">
                                {{ __('jobapplication.wfh') }}</p>
                            <p class="card-text">{{ __('jobapplication.wfh_text') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card">
                        <img src="/storage/webstyle/lion_think.webp" class="card-img-top" alt="...">
                        <div class="card-body">
                            <p class="card-title text-info" style="text-decoration-line: underline;">
                                {{ __('jobapplication.newIDA') }}</p>
                            <p class="card-text">{{ __('jobapplication.newIDA_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
        </section>


    </div>
@endsection
