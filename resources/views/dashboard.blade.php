@extends('admin.layouts.app')

@section('content')
    <style>
        :root {
            --dash-bg: #f6f9fc;
            --dash-card: #ffffff;
            --dash-line: #d9e3f2;
            --dash-text: #112240;
            --dash-muted: #5b6b84;
            --dash-primary: #0b72ff;
            --dash-success: #16a34a;
            --dash-danger: #c0262d;
            --dash-warning: #ea580c;
        }

        .premium-shell {
            position: relative;
            background: linear-gradient(170deg, #f8fbff, #f2f7ff 42%, #edf4ff 100%);
            border: 1px solid var(--dash-line);
            border-radius: 22px;
            padding: 1.2rem;
            overflow: hidden;
            box-shadow: 0 20px 42px rgba(15, 43, 88, 0.08);
        }

        .premium-shell::before,
        .premium-shell::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            z-index: 0;
        }

        .premium-shell::before {
            width: 260px;
            height: 260px;
            top: -140px;
            right: -90px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0));
        }

        .premium-shell::after {
            width: 220px;
            height: 220px;
            left: -90px;
            bottom: -120px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.18), rgba(59, 130, 246, 0));
        }

        .premium-shell > * {
            position: relative;
            z-index: 1;
        }

        .hero-card {
            background: linear-gradient(125deg, #ffffff, #f7fbff 65%, #eef6ff);
            border: 1px solid #dbe9fb;
            border-radius: 16px;
            padding: 0.95rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.9rem;
            margin-bottom: 0.9rem;
        }

        .hero-title {
            margin: 0;
            color: var(--dash-text);
            font-size: 1.18rem;
            font-weight: 700;
        }

        .hero-sub {
            margin: 0.2rem 0 0;
            color: var(--dash-muted);
            font-size: 0.88rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            justify-content: flex-end;
        }

        .hero-actions .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .hero-actions .form-select {
            border-radius: 10px;
            min-width: 140px;
            border: 1px solid #c6d9f6;
            box-shadow: 0 0 0 4px rgba(14, 116, 255, 0.06);
        }

        .print-group {
            display: inline-flex;
            gap: 0.35rem;
            padding: 0.22rem;
            border-radius: 10px;
            border: 1px solid #d3e2f7;
            background: #f7fbff;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .kpi-card {
            background: var(--dash-card);
            border: 1px solid #dce7f8;
            border-radius: 14px;
            padding: 0.85rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .kpi-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            margin-bottom: 0.35rem;
        }

        .kpi-label {
            color: var(--dash-muted);
            font-size: 0.82rem;
            margin: 0;
        }

        .kpi-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: #fff;
        }

        .kpi-icon.enroll {
            background: linear-gradient(140deg, #0ea5e9, #0b72ff);
        }

        .kpi-icon.income {
            background: linear-gradient(140deg, #22c55e, #16a34a);
        }

        .kpi-icon.register {
            background: linear-gradient(140deg, #f97316, #ea580c);
        }

        .kpi-value {
            margin: 0;
            color: #0b1d3a;
            font-size: 1.4rem;
            line-height: 1.2;
            font-weight: 700;
        }

        .kpi-meta {
            margin: 0.28rem 0 0;
            color: var(--dash-muted);
            font-size: 0.8rem;
        }

        .trend-up {
            color: var(--dash-success);
            font-weight: 700;
        }

        .trend-down {
            color: var(--dash-danger);
            font-weight: 700;
        }

        .insight-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }

        .insight-card {
            background: linear-gradient(145deg, #ffffff, #f9fcff);
            border: 1px solid #d8e6fb;
            border-radius: 12px;
            padding: 0.65rem 0.75rem;
        }

        .insight-card p {
            margin: 0;
            color: var(--dash-muted);
            font-size: 0.76rem;
        }

        .insight-card h6 {
            margin: 0.2rem 0 0;
            color: #0f2345;
            font-size: 1rem;
            font-weight: 700;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 0.8rem;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #dce8f8;
            border-radius: 14px;
            padding: 0.88rem;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.55rem;
        }

        .panel-title {
            margin: 0;
            color: #102446;
            font-size: 0.94rem;
            font-weight: 700;
        }

        .panel-sub {
            margin: 0;
            color: var(--dash-muted);
            font-size: 0.77rem;
        }

        .timeline-tools {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .timeline-tools .btn {
            border-radius: 9px;
            font-weight: 600;
            padding: 0.26rem 0.58rem;
            line-height: 1.25;
        }

        .zoom-state {
            display: inline-flex;
            align-items: center;
            border: 1px solid #d5e4f8;
            background: #f4f9ff;
            color: #335274;
            border-radius: 999px;
            padding: 0.17rem 0.54rem;
            font-size: 0.74rem;
            font-weight: 700;
        }

        #dashboardTimelineChart {
            min-height: 340px;
        }

        .table-wrap {
            max-height: 310px;
            overflow-y: auto;
            border: 1px solid #e3ebf8;
            border-radius: 10px;
        }

        .table-wrap thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f3f8ff;
            color: #294264;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table-wrap tbody td {
            font-size: 0.83rem;
            color: #20324d;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            max-height: 620px;
            overflow-y: auto;
            padding-right: 0.2rem;
        }

        .activity-item {
            border: 1px solid #e0e9f7;
            border-radius: 10px;
            background: linear-gradient(145deg, #ffffff, #f9fcff);
            padding: 0.6rem 0.65rem;
        }

        .activity-time {
            color: #597193;
            font-size: 0.73rem;
            margin-bottom: 0.15rem;
            display: inline-block;
        }

        .activity-text {
            margin: 0;
            color: #182d4a;
            font-size: 0.84rem;
        }

        @media (max-width: 1200px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .activity-list {
                max-height: 320px;
            }
        }

        @media (max-width: 992px) {
            .kpi-grid,
            .insight-grid {
                grid-template-columns: 1fr;
            }

            .hero-actions {
                justify-content: flex-start;
            }

            #dashboardTimelineChart {
                min-height: 300px;
            }

            .timeline-tools {
                justify-content: flex-start;
            }
        }
    </style>

    <section class="section dashboard">
        <div class="premium-shell">
            <div class="hero-card">
                <div>
                    <h5 class="hero-title">Executive Reporting Dashboard</h5>
                    <p class="hero-sub">Premium analytics surface with live range controls, validated KPIs, export and multi-paper print.</p>
                </div>

                <div class="hero-actions">
                    <select id="rangeDays" class="form-select form-select-sm" aria-label="Report days range">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="180">Last 180 days</option>
                        <option value="365">Last 365 days</option>
                    </select>

                    <a id="exportCsvLink" class="btn btn-sm btn-primary" href="{{ route('dashboard.export.csv', ['days' => 30]) }}">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>

                    <div class="print-group">
                        <a id="printA5Link" class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('dashboard.print', ['paper' => 'A5', 'days' => 30]) }}">A5</a>
                        <a id="printA4Link" class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('dashboard.print', ['paper' => 'A4', 'days' => 30]) }}">A4</a>
                        <a id="printA3Link" class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('dashboard.print', ['paper' => 'A3', 'days' => 30]) }}">A3</a>
                        <a id="printA1Link" class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('dashboard.print', ['paper' => 'A1', 'days' => 30]) }}">A1</a>
                    </div>
                </div>
            </div>

            <div class="kpi-grid">
                <article class="kpi-card">
                    <div class="kpi-head">
                        <p class="kpi-label">Enrollments (Current Month)</p>
                        <span class="kpi-icon enroll"><i class="bi bi-journal-check"></i></span>
                    </div>
                    <h6 class="kpi-value">{{ number_format($metrics['enrollments']['current']) }}</h6>
                    <p class="kpi-meta">
                        Previous: {{ number_format($metrics['enrollments']['previous']) }} |
                        <span class="{{ $metrics['enrollments']['trend'] === 'up' ? 'trend-up' : 'trend-down' }}">
                            {{ number_format($metrics['enrollments']['change'], 2) }}%
                        </span>
                    </p>
                </article>

                <article class="kpi-card">
                    <div class="kpi-head">
                        <p class="kpi-label">Income (Current Month)</p>
                        <span class="kpi-icon income"><i class="bi bi-cash-coin"></i></span>
                    </div>
                    <h6 class="kpi-value">{{ number_format($metrics['income']['current_mmk']) }} MMK</h6>
                    <p class="kpi-meta">
                        USD {{ number_format($metrics['income']['current_usd'], 2) }} |
                        <span class="{{ $metrics['income']['trend'] === 'up' ? 'trend-up' : 'trend-down' }}">
                            {{ number_format($metrics['income']['change'], 2) }}%
                        </span>
                    </p>
                </article>

                <article class="kpi-card">
                    <div class="kpi-head">
                        <p class="kpi-label">Registrations (Current Month)</p>
                        <span class="kpi-icon register"><i class="bi bi-people"></i></span>
                    </div>
                    <h6 class="kpi-value">{{ number_format($metrics['registrations']['current']) }}</h6>
                    <p class="kpi-meta">
                        Previous: {{ number_format($metrics['registrations']['previous']) }} |
                        <span class="{{ $metrics['registrations']['trend'] === 'up' ? 'trend-up' : 'trend-down' }}">
                            {{ number_format($metrics['registrations']['change'], 2) }}%
                        </span>
                    </p>
                </article>
            </div>

            <div class="insight-grid">
                <div class="insight-card">
                    <p>Selected Range Enrollments</p>
                    <h6 id="rangeEnrollTotal">0</h6>
                </div>
                <div class="insight-card">
                    <p>Selected Range Income (MMK)</p>
                    <h6 id="rangeIncomeTotal">0</h6>
                </div>
                <div class="insight-card">
                    <p>Selected Range Registrations</p>
                    <h6 id="rangeRegisterTotal">0</h6>
                </div>
            </div>

            <div class="content-grid">
                <div class="panel">
                    <div class="panel-head">
                        <div>
                            <h6 class="panel-title">Performance Timeline</h6>
                            <p class="panel-sub">Interactive trend lines with synchronized daily table.</p>
                        </div>
                        <div class="timeline-tools">
                            <button type="button" id="zoomOutBtn" class="btn btn-sm btn-outline-secondary">Zoom -</button>
                            <button type="button" id="zoomResetBtn" class="btn btn-sm btn-outline-secondary">Reset</button>
                            <button type="button" id="zoomInBtn" class="btn btn-sm btn-outline-primary">Zoom +</button>
                            <span id="zoomState" class="zoom-state">View 30d / 30d</span>
                        </div>
                    </div>

                    <div id="dashboardTimelineChart"></div>

                    <hr class="my-3">

                    <h6 class="panel-title mb-2">Daily Breakdown</h6>
                    <div class="table-wrap">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Enrollments</th>
                                    <th>Income (MMK)</th>
                                    <th>Registrations</th>
                                </tr>
                            </thead>
                            <tbody id="dailyReportTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <div>
                            <h6 class="panel-title">Recent Activity</h6>
                            <p class="panel-sub">Latest admin-targeted logs.</p>
                        </div>
                    </div>

                    @if($latestActivities->isEmpty())
                        <p class="text-secondary mb-0">No activity found.</p>
                    @else
                        <div class="activity-list">
                            @foreach($latestActivities as $activity)
                                <div class="activity-item">
                                    <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                                    <p class="activity-text">{{ $activity->short }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chartPayload = @json($chartData);
            const rangeDaysEl = document.getElementById('rangeDays');
            const dailyTableBody = document.getElementById('dailyReportTableBody');
            const rangeEnrollTotal = document.getElementById('rangeEnrollTotal');
            const rangeIncomeTotal = document.getElementById('rangeIncomeTotal');
            const rangeRegisterTotal = document.getElementById('rangeRegisterTotal');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const zoomResetBtn = document.getElementById('zoomResetBtn');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomState = document.getElementById('zoomState');

            const exportCsvLink = document.getElementById('exportCsvLink');
            const printA5Link = document.getElementById('printA5Link');
            const printA4Link = document.getElementById('printA4Link');
            const printA3Link = document.getElementById('printA3Link');
            const printA1Link = document.getElementById('printA1Link');

            const routeBaseExport = "{{ route('dashboard.export.csv') }}";
            const routeBasePrintA5 = "{{ route('dashboard.print', ['paper' => 'A5']) }}";
            const routeBasePrintA4 = "{{ route('dashboard.print', ['paper' => 'A4']) }}";
            const routeBasePrintA3 = "{{ route('dashboard.print', ['paper' => 'A3']) }}";
            const routeBasePrintA1 = "{{ route('dashboard.print', ['paper' => 'A1']) }}";

            const allLabels = chartPayload.labels || [];
            const allEnrollments = (chartPayload.enrollments || []).map((value) => Number(value) || 0);
            const allIncome = (chartPayload.income || []).map((value) => Number(value) || 0);
            const allRegistrations = (chartPayload.registrations || []).map((value) => Number(value) || 0);

            let timelineChart = null;
            const MIN_VISIBLE_DAYS = 7;
            let selectedRangeDays = Number(rangeDaysEl?.value || 30);
            let visibleRangeDays = selectedRangeDays;

            function formatNumber(value) {
                return Number(value || 0).toLocaleString('en-US');
            }

            function filterSeries(days) {
                const keep = Math.max(1, Math.min(days, allLabels.length));
                const start = allLabels.length - keep;

                return {
                    labels: allLabels.slice(start),
                    enrollments: allEnrollments.slice(start),
                    income: allIncome.slice(start),
                    registrations: allRegistrations.slice(start),
                };
            }

            function renderTable(series) {
                dailyTableBody.innerHTML = '';

                for (let i = series.labels.length - 1; i >= 0; i--) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${series.labels[i]}</td>
                        <td>${formatNumber(series.enrollments[i])}</td>
                        <td>${formatNumber(series.income[i])}</td>
                        <td>${formatNumber(series.registrations[i])}</td>
                    `;
                    dailyTableBody.appendChild(tr);
                }
            }

            function renderChart(series) {
                if (timelineChart) {
                    timelineChart.destroy();
                }

                const chartEl = document.querySelector('#dashboardTimelineChart');
                if (!chartEl) {
                    return;
                }

                if (typeof ApexCharts === 'undefined') {
                    chartEl.innerHTML = '<div class="text-danger">Chart library not loaded.</div>';
                    return;
                }

                timelineChart = new ApexCharts(document.querySelector('#dashboardTimelineChart'), {
                    chart: {
                        type: 'line',
                        height: 380,
                        toolbar: { show: true },
                        foreColor: '#385170',
                        zoom: { enabled: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 650,
                            animateGradually: { enabled: true, delay: 90 },
                            dynamicAnimation: { enabled: true, speed: 240 },
                        },
                        dropShadow: {
                            enabled: true,
                            top: 2,
                            left: 0,
                            blur: 6,
                            opacity: 0.22,
                        }
                    },
                    stroke: {
                        width: [6, 6, 6],
                        curve: 'smooth',
                        lineCap: 'round',
                    },
                    markers: {
                        size: [4.5, 4.5, 4.5],
                        strokeWidth: 3,
                        strokeColors: '#ffffff',
                        hover: { size: 8 },
                    },
                    series: [
                        { name: 'Enrollments', data: series.enrollments },
                        { name: 'Income (MMK)', data: series.income },
                        { name: 'Registrations', data: series.registrations },
                    ],
                    colors: ['#00A8FF', '#00C48C', '#FF6B6B'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            shadeIntensity: 0.35,
                            type: 'vertical',
                            opacityFrom: 0.24,
                            opacityTo: 0.04,
                            stops: [0, 72, 100],
                        },
                    },
                    xaxis: {
                        categories: series.labels,
                        labels: { rotate: -35 },
                        axisBorder: { color: '#d8e4f5' },
                        axisTicks: { color: '#d8e4f5' },
                    },
                    yaxis: [
                        {
                            seriesName: 'Enrollments',
                            title: { text: 'Counts' },
                            labels: {
                                formatter: (value) => Number(value).toLocaleString('en-US'),
                            },
                        },
                        {
                            seriesName: 'Income (MMK)',
                            opposite: true,
                            title: { text: 'MMK' },
                            labels: {
                                formatter: (value) => Number(value).toLocaleString('en-US'),
                            },
                        },
                        {
                            seriesName: 'Registrations',
                            show: false,
                        },
                    ],
                    grid: {
                        borderColor: '#e2ebf8',
                        strokeDashArray: 4,
                        row: {
                            colors: ['#f9fcff', 'transparent'],
                            opacity: 0.65,
                        },
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        fontSize: '13px',
                        fontWeight: 600,
                        labels: { colors: '#2f496a' },
                        markers: { width: 10, height: 10, radius: 12, offsetX: -1 },
                        itemMargin: { horizontal: 10, vertical: 4 },
                    },
                    dataLabels: { enabled: false },
                    tooltip: {
                        theme: 'light',
                        shared: true,
                        intersect: false,
                        x: { show: true },
                        y: {
                            formatter: (value, { seriesIndex }) => {
                                const numeric = Number(value || 0);
                                if (seriesIndex === 1) {
                                    return `${numeric.toLocaleString('en-US')} MMK`;
                                }
                                return numeric.toLocaleString('en-US');
                            }
                        }
                    },
                    theme: {
                        mode: 'light',
                    },
                    noData: {
                        text: 'No chart data available for selected range.',
                    },
                });

                timelineChart.render();
            }

            function updateRangeInsights(series) {
                const enrollTotal = series.enrollments.reduce((sum, item) => sum + Number(item || 0), 0);
                const incomeTotal = series.income.reduce((sum, item) => sum + Number(item || 0), 0);
                const registerTotal = series.registrations.reduce((sum, item) => sum + Number(item || 0), 0);

                rangeEnrollTotal.textContent = formatNumber(enrollTotal);
                rangeIncomeTotal.textContent = formatNumber(incomeTotal);
                rangeRegisterTotal.textContent = formatNumber(registerTotal);
            }

            function updateActionLinks(days) {
                exportCsvLink.href = `${routeBaseExport}?days=${days}`;
                printA5Link.href = `${routeBasePrintA5}?days=${days}`;
                printA4Link.href = `${routeBasePrintA4}?days=${days}`;
                printA3Link.href = `${routeBasePrintA3}?days=${days}`;
                printA1Link.href = `${routeBasePrintA1}?days=${days}`;
            }

            function updateZoomState() {
                const clampedSelected = Math.max(1, selectedRangeDays);
                const clampedVisible = Math.max(MIN_VISIBLE_DAYS, Math.min(visibleRangeDays, clampedSelected));
                visibleRangeDays = clampedVisible;

                if (zoomState) {
                    zoomState.textContent = `View ${clampedVisible}d / ${clampedSelected}d`;
                }

                if (zoomInBtn) {
                    zoomInBtn.disabled = clampedVisible <= MIN_VISIBLE_DAYS;
                }

                if (zoomOutBtn) {
                    zoomOutBtn.disabled = clampedVisible >= clampedSelected;
                }

                if (zoomResetBtn) {
                    zoomResetBtn.disabled = clampedVisible === clampedSelected;
                }
            }

            function refreshDashboard() {
                const series = filterSeries(visibleRangeDays);
                renderChart(series);
                renderTable(series);
                updateRangeInsights(series);
                updateActionLinks(selectedRangeDays);
                updateZoomState();
            }

            rangeDaysEl.addEventListener('change', function () {
                selectedRangeDays = Number(this.value) || 30;
                visibleRangeDays = selectedRangeDays;
                refreshDashboard();
            });

            zoomInBtn?.addEventListener('click', function () {
                visibleRangeDays = Math.max(MIN_VISIBLE_DAYS, Math.ceil(visibleRangeDays / 2));
                refreshDashboard();
            });

            zoomOutBtn?.addEventListener('click', function () {
                visibleRangeDays = Math.min(selectedRangeDays, visibleRangeDays * 2);
                refreshDashboard();
            });

            zoomResetBtn?.addEventListener('click', function () {
                visibleRangeDays = selectedRangeDays;
                refreshDashboard();
            });

            refreshDashboard();
        });
    </script>
@endsection
