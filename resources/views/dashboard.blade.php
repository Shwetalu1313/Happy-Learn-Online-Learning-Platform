@extends('admin.layouts.app')
@section('content')
    @php
    use App\Models\CourseEnrollUser;
    use App\Models\User;
    use App\Models\SystemActivity;

    $enrollReport = CourseEnrollUser::generateReport();
    $incomeReport = CourseEnrollUser::incomeReport();
    $registerReport = User::usersRegisterCount();
    $latestActivities = SystemActivity::getData(false,true);

    $enrollJson = CourseEnrollUser::getEnrollmentsChartData()->toJson();
    $incomeJson = CourseEnrollUser::getIncomeChartData()->toJson();
    $registerJson = User::getRegisterUserChartData()->toJson();

    $textColorArray = ['text-info','text-warning','text-danger','text-primary','text-secondary', 'text-info-emphasis']
 @endphp
    <section class="section dashboard">

        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card text-info-emphasis sales-card">

                            <div class="card-body">
                                <h5 class="card-title">Enrolls <span>| This month</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-journal-bookmark"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 title="enroll count">{{$enrollReport['current_month_enrollments']}}</h6>
                                        <span class="@if($enrollReport['improved']) text-success @else text-danger @endif  small pt-1 fw-bold">{{$enrollReport['percentage_change']}}%</span> <span class="text-muted small pt-2 ps-1">@if($enrollReport['improved']) increase @else descrease @endif</span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End enroll Card -->

                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="card-body">
                                <h5 class="card-title">Income <span>| This month</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-currency-dollar text-success"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 title="US Dollar Value">{{$incomeReport['current_month_income']}}</h6>
                                        <span class="@if($incomeReport['increased']) text-success @else text-danger @endif  small pt-1 fw-bold">{{$incomeReport['percentage_change']}}%</span> <span class="text-muted small pt-2 ps-1">@if($incomeReport['increased']) increase @else descrease @endif</span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Income Card -->

                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="card-body">
                                <h5 class="card-title">Registers <span>| 3 month</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people text-warning"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 title="users">{{$registerReport['current_month_count']}}</h6>
                                        <span class="@if($registerReport['increased']) text-success @else text-danger @endif  small pt-1 fw-bold">{{$registerReport['previous_three_months_count']}}%</span> <span class="text-muted small pt-2 ps-1">@if($registerReport['increased']) increase @else descrease @endif</span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Register Card -->


                    <div class="col-12">
                        <div class="card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>
                            <div class="bb" id="bb"></div>
                            <div class="card-body">
                                <h5 class="card-title">Reports <span>/ Income Timeline</span></h5>

                                <!-- Area Chart -->
                                <div id="areaChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        const incomeData = {!! $incomeJson !!};

                                        // Log the income data to check its structure
                                        console.log('Income data:', incomeData);

                                        const incomeValues = incomeData.map(entry => entry.income);
                                        const incomeDates = incomeData.map(entry => {
                                            const dateObj = new Date(entry.date);
                                            return `${dateObj.toLocaleString('default', { month: 'short' })} ${dateObj.getDate()}`;
                                        });


                                        new ApexCharts(document.querySelector("#areaChart"), {
                                            series: [{
                                                name: "Income",
                                                data: incomeValues,
                                            }],
                                            chart: {
                                                type: 'area',
                                                height: 350,
                                                zoom: {
                                                    enabled: false
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'straight'
                                            },
                                            subtitle: {
                                                text: 'Prize Movements',
                                                align: 'left'
                                            },
                                            labels: incomeDates,
                                            xaxis: {
                                                type: 'datetime',
                                            },
                                            yaxis: {
                                                opposite: true
                                            },
                                            legend: {
                                                horizontalAlign: 'left'
                                            }
                                        }).render();
                                    });
                                </script>
                                <!-- End Area Chart -->



                            </div>

                        </div>
                    </div><!-- End Chart Reports -->

                </div>
            </div>

            {{--left side column--}}
            <div class="col-lg-4">

                {{--Rescent Activity--}}
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>List</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">More</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Recent Activity </h5>

                        @if($latestActivities->count() === 0)

                            <div class="text-center">
                                <img src="{{asset('./storage/webstyle/svg/file_error_not_found.svg')}}" class="w-50 h-50 my-3 text-center text-secondary" >

                                <div class="text-centert fs-5 text-secondary-emphasis">No Activity Found</div>
                            </div>

                        @else
                            <div class="activity mb-2">
                                @foreach($latestActivities as $i => $activity)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $activity->created_at->diffForHumans() }}</div>
                                        @php
                                            $textColorIndex = $i % count($textColorArray);
                                            $textColor = $textColorArray[$textColorIndex];
                                        @endphp
                                        <i class='bi bi-circle-fill activity-badge {{ $textColor }} align-self-start'></i>

                                        <div class="activity-content">
                                            {{ $activity->short }}
                                        </div>
                                    </div><!-- End activity item-->
                                @endforeach
                            </div>
                        @endif

                    </div>

                </div>
            </div>
            {{--end left side column--}}
        </div>
    </section>
@endsection
