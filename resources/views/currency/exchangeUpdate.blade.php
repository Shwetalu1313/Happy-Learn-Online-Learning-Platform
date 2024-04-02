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

    <div class="card">
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-VK21DW5DTL');
        </script>
        <script>
            // Fetch data from API endpoints
            var startDate = moment("2024-02-07"); // Example start date
            var endDate = moment(new Date()).subtract(1, "days"); // Example end date

            // Function to find all weekdays within the date range
            function findWeekdaysInRange(startDate, endDate) {
                const weekdays = [];
                let currentDate = moment(startDate);

                // Loop through each date in the range
                while (currentDate.isSameOrBefore(endDate)) {
                    // Check if the current date is a weekday (Monday to Friday)
                    if (currentDate.isoWeekday() >= 1 && currentDate.isoWeekday() <= 5) {
                        // If it's a weekday, add it to the weekdays array
                        weekdays.push(currentDate.format("YYYY/MM/DD"));
                    }
                    // Move to the next day
                    currentDate.add(1, "days");
                }
                return weekdays;
            }

            // Draw Chart Method

            function DrawChart(labels = [], datasets = [], canvasSelector, scales) {
                const chartData = {
                    labels,

                    datasets,
                };

                let ctx = document.querySelector(canvasSelector).getContext("2d");

                let chart = new Chart(ctx, {
                    type: "line",
                    data: chartData,

                    options: {
                        scales: scales,
                    },
                });
            }

            // Call the function and log the result
            var weekdaysInRange = findWeekdaysInRange(startDate, endDate);
            var fetchEndpoints = weekdaysInRange
                .slice(-30)
                .map((i) =>
                    fetch(`https://myanmar-currency-api.github.io/api/${i}/latest.json`)
                );
            Promise.all(fetchEndpoints)
                .then((responses) =>
                    Promise.all(responses.map((response) => response.json()))
                )
                .then((data) => {
                    // Merge data into a single dataset

                    const mergedData = [];
                    data.forEach((dayData) => {
                        dayData.data.forEach((currency) => {
                            const existingCurrency = mergedData.find(
                                (item) => item.currency === currency.currency
                            );
                            if (existingCurrency) {
                                existingCurrency.data.push({
                                    timestamp: dayData.timestamp,
                                    buy: parseFloat(currency.buy),
                                    sell: parseFloat(currency.sell),
                                });
                            } else {
                                mergedData.push({
                                    currency: currency.currency,
                                    data: [
                                        {
                                            timestamp: dayData.timestamp,
                                            buy: parseFloat(currency.buy),
                                            sell: parseFloat(currency.sell),
                                        },
                                    ],
                                });
                            }
                        });
                    });

                    // Extract weekdays' data only
                    const weekdaysData = mergedData.map((currency) => ({
                        currency: currency.currency,
                        data: currency.data.filter((item) => {
                            const timestamp = new Date(item.timestamp);
                            const dayOfWeek = timestamp.getDay();
                            return dayOfWeek !== 0 && dayOfWeek !== 6; // Filter out weekends
                        }),
                    }));
                    const labels = weekdaysData.map((currency) =>
                        currency.data.map((item) => {
                            return new Date(item.timestamp);
                        })
                    )[0];

                    // Prepare datasets for Chart.js
                    const buyDataSets = weekdaysData.map((currency) => ({
                        label: currency.currency,
                        borderColor: getRandomColor(),
                        data: currency.data.map((item) => item.buy),
                        hidden: !(currency.currency === "USD"),
                    }));

                    const sellDataSets = weekdaysData.map((currency) => ({
                        label: currency.currency,
                        borderColor: getRandomColor(),
                        data: currency.data.map((item) => item.sell),
                        hidden: !(currency.currency === "USD"),
                    }));

                    const averageDataSets = weekdaysData.map((currency) => ({
                        label: currency.currency,
                        borderColor: getRandomColor(),
                        data: currency.data.map((item) => (item.sell + item.buy) / 2),
                        hidden: !(currency.currency === "USD"),
                    }));

                    DrawChart(labels, buyDataSets, "#buyExchangeRateChart", {
                        x: {
                            type: "time",
                            time: {
                                unit: "day",
                            },
                            ticks: {
                                source: "auto",
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: "Exchange Rate Buy",
                            },
                        },
                    });

                    DrawChart(labels, sellDataSets, "#sellExchangeRateChart", {
                        x: {
                            type: "time",
                            time: {
                                unit: "day",
                            },
                            ticks: {
                                source: "auto",
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: "Exchange Rate Sell",
                            },
                        },
                    });

                    DrawChart(labels, averageDataSets, "#averageExchangeRateChart", {
                        x: {
                            type: "time",
                            time: {
                                unit: "day",
                            },
                            ticks: {
                                source: "auto",
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: "Exchange Average Buy & Sell",
                            },
                        },
                    });
                })
                .catch((error) => console.error("Error fetching data:", error));
            // Create Chart.js instance

            // Function to generate random color
            function getRandomColor() {
                const letters = "0123456789ABCDEF";
                let color = "#";
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
        </script>
    </div>
@endsection
