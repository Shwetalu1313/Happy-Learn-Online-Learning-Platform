@extends('admin.layouts.app')

@section('content')

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

    <div id="hero"></div>

    <script>
        //const axios = require('axios/dist/browser/axios.cjs');
        const heroID = document.getElementById('hero');


        let url = "https://cors-anywhere.herokuapp.com/http://forex.cbm.gov.mm/api/latest";
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.onreadystatechange = function (){
            if(this.status === 200 && this.onreadystatechange == 4){
                heroID.innerText = this.responseText;
                console.log(this.responseText);
            }
        }
    xhr.send();

        // const axios = require('axios/dist/node/axios.cjs')
        // axios.get('http://forex.cbm.gov.mm/api/latest')
        //     .then(function (response) {
        //         console.log(response);
        //     })
        //     .catch(function (error) {
        //         // handle error
        //         console.log(error);
        //     })
    </script>

    <div class="card p-5">
        <h3 class="card-title text-primary-emphasis">Exchange Rate Modify</h3>
        <form action="{{route('usUpdate')}}" method="POST" class="mb-5 shadow-sm p-5 d-flex flex-column justify-content-center">
            @method('PUT')
            @csrf
            <div class="input-group mb-3 ">
                <label for="us_ex" class="col-sm-2 col-form-label">
                    1 {{ __('nav.us_dol') }} <i class="bi bi-shuffle text-danger"></i>
                </label>
                    <input type="number" min="0" class="form-control" name="us_ex" id="us_ex" value="{{ $exchange->us_ex }}">
                    <span class="input-group-text">{{ __('nav.mmk') }}</span>
            </div>
            <div class="text-end">
                <button class="btn btn-mb btn-secondary">{{ __('btnText.confirm') }}</button>
            </div>

        </form>


        <form action="{{route('ptsUpdate')}}" method="POST" class="mb-5 shadow-sm p-5 d-flex flex-column justify-content-center">
            @method('PUT')
            @csrf
            <div class="input-group mb-3 ">
                <label for="us_ex" class="col-sm-2 col-form-label">
                    1 {{ __('nav.pts') }} <i class="bi bi-shuffle text-danger"></i>
                </label>
                <input type="number" min="0" class="form-control" name="pts_ex" id="pts_ex" value="{{ $exchange->pts_ex }}">
                <span class="input-group-text">{{ __('nav.mmk') }}</span>
            </div>
            <div class="text-end">
                <button class="btn btn-mb btn-secondary">{{ __('btnText.confirm') }}</button>
            </div>
        </form>
    </div>

    <div class="card px-5 py-3 bg-secondary-subtle shadow-sm">
        <h3 class="card-title text-primary-emphasis">Exchange Rate</h3>
        <div class="d-flex justify-content-around">
            <div class="d-flex w-75 justify-content-between align-items-center">
                <p class="fs-2 text-info-emphasis">1 {{ __('nav.us_dol') }}</p>
                <i class="bi bi-arrow-left-right fs-4 text-secondary-emphasis"></i>
                <p class="fs-2 text-info-emphasis">{{ $exchange->us_ex .' '.__('nav.mmk') }}</p>
            </div>
        </div>
        <hr class="w-50 mx-auto">
        <div class="d-flex justify-content-around">
            <div class="d-flex w-75 justify-content-between align-items-center">
                <p class="fs-2 text-info-emphasis">1 {{ __('nav.pts') }}</p>
                <i class="bi bi-arrow-left-right fs-4 text-secondary-emphasis"></i>
                <p class="fs-2 text-info-emphasis">{{ $exchange->pts_ex .' '.__('nav.mmk') }}</p>
            </div>
        </div>
    </div>
@endsection
