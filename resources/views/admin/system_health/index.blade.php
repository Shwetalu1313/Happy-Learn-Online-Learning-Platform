@extends('admin.layouts.app')

@section('content')
    <style>
        .health-shell {
            border: 1px solid #d7e5f6;
            border-radius: 18px;
            padding: 1rem;
            background: linear-gradient(160deg, #f7fbff 0%, #eef5ff 100%);
            box-shadow: 0 12px 30px rgba(18, 58, 110, 0.08);
        }

        .health-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-bottom: 0.9rem;
        }

        .health-title {
            margin: 0;
            color: #102544;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .health-sub {
            margin: 0.15rem 0 0;
            color: #476282;
            font-size: 0.84rem;
        }

        .health-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(200px, 1fr));
            gap: 0.7rem;
            margin-bottom: 0.8rem;
        }

        .health-card {
            background: #fff;
            border: 1px solid #dbe7f7;
            border-radius: 12px;
            padding: 0.7rem;
        }

        .health-card p {
            margin: 0;
            color: #5a7392;
            font-size: 0.78rem;
        }

        .health-card h6 {
            margin: 0.24rem 0 0;
            font-size: 1.18rem;
            color: #122947;
            font-weight: 700;
        }

        .health-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 0.75rem;
        }

        .panel {
            background: #fff;
            border: 1px solid #dbe7f7;
            border-radius: 12px;
            padding: 0.75rem;
        }

        .panel-title {
            margin: 0 0 0.55rem;
            color: #102a49;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .stat-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(130px, 1fr));
            gap: 0.5rem;
        }

        .stat-item {
            border: 1px solid #e4edf9;
            border-radius: 10px;
            padding: 0.55rem;
            background: #f8fbff;
        }

        .stat-item dt {
            margin: 0;
            font-size: 0.74rem;
            color: #607b9b;
            font-weight: 600;
        }

        .stat-item dd {
            margin: 0.2rem 0 0;
            font-size: 0.9rem;
            color: #173253;
            font-weight: 700;
        }

        .badge-up {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-down {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .mini-meta {
            margin-top: 0.6rem;
            font-size: 0.76rem;
            color: #607b9b;
        }

        #minuteTrafficChart,
        #hourlyLoadChart {
            min-height: 320px;
        }

        @media (max-width: 1200px) {
            .health-cards {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }

            .health-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .health-cards {
                grid-template-columns: 1fr;
            }

            .stat-list {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="section dashboard">
        <div class="health-shell">
            <div class="health-top">
                <div>
                    <h5 class="health-title">System Health Dashboard</h5>
                    <p class="health-sub">Live operational visibility for traffic, API load, database, server, and container runtime.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="refreshHealthBtn" class="btn btn-sm btn-primary" type="button">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <small class="text-secondary">Updated: <span id="updatedAtText">-</span></small>
                </div>
            </div>

            <div class="health-cards">
                <article class="health-card">
                    <p>Responses / Minute</p>
                    <h6 id="rpmValue">0</h6>
                </article>
                <article class="health-card">
                    <p>Responses / Hour</p>
                    <h6 id="rphValue">0</h6>
                </article>
                <article class="health-card">
                    <p>Avg Response (1m)</p>
                    <h6 id="avg1mValue">0 ms</h6>
                </article>
                <article class="health-card">
                    <p>Error Rate (1h)</p>
                    <h6 id="errorRateValue">0%</h6>
                </article>
            </div>

            <div class="health-grid">
                <div class="panel">
                    <h6 class="panel-title">Live Request Load (Last 60 Minutes)</h6>
                    <div id="minuteTrafficChart"></div>
                </div>

                <div class="panel">
                    <h6 class="panel-title">Platform Components</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span id="dbStatusBadge" class="badge rounded-pill badge-up">DB UP</span>
                        <span class="small text-secondary" id="dbLatencyText">-</span>
                    </div>
                    <dl class="stat-list">
                        <div class="stat-item">
                            <dt>API RPM / RPH</dt>
                            <dd id="apiTrafficText">0 / 0</dd>
                        </div>
                        <div class="stat-item">
                            <dt>Web RPM / RPH</dt>
                            <dd id="webTrafficText">0 / 0</dd>
                        </div>
                        <div class="stat-item">
                            <dt>Server Load (1/5/15m)</dt>
                            <dd id="serverLoadText">-</dd>
                        </div>
                        <div class="stat-item">
                            <dt>Memory (Used/Peak)</dt>
                            <dd id="serverMemoryText">-</dd>
                        </div>
                        <div class="stat-item">
                            <dt>Container Runtime</dt>
                            <dd id="containerText">-</dd>
                        </div>
                        <div class="stat-item">
                            <dt>Routes (Total/API/Web)</dt>
                            <dd id="routesText">-</dd>
                        </div>
                    </dl>
                    <p class="mini-meta mb-0">
                        Started at: <span id="startedAtText">-</span>
                    </p>
                </div>
            </div>

            <div class="panel mt-3">
                <h6 class="panel-title">Hourly Throughput (Last 24 Hours)</h6>
                <div id="hourlyLoadChart"></div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initialSnapshot = @json($healthSnapshot);
            const snapshotUrl = "{{ route('admin.system-health.snapshot') }}";
            const refreshButton = document.getElementById('refreshHealthBtn');

            let minuteTrafficChart = null;
            let hourlyLoadChart = null;

            function numberFormat(value) {
                return Number(value || 0).toLocaleString('en-US');
            }

            function text(id, value) {
                const element = document.getElementById(id);
                if (!element) {
                    return;
                }
                element.textContent = value;
            }

            function updateStatusBadge(status) {
                const badge = document.getElementById('dbStatusBadge');
                if (!badge) {
                    return;
                }

                if (status === 'UP') {
                    badge.classList.remove('badge-down');
                    badge.classList.add('badge-up');
                    badge.textContent = 'DB UP';
                    return;
                }

                badge.classList.remove('badge-up');
                badge.classList.add('badge-down');
                badge.textContent = 'DB DOWN';
            }

            function renderCharts(snapshot) {
                const chartPayload = snapshot.charts || {};
                const minuteLabels = chartPayload.minute_labels || [];
                const minuteRequests = chartPayload.minute_requests || [];
                const minuteApiRequests = chartPayload.minute_api_requests || [];
                const minuteAvgResponse = chartPayload.minute_avg_response_ms || [];

                const hourLabels = chartPayload.hour_labels || [];
                const hourRequests = chartPayload.hour_requests || [];

                if (typeof ApexCharts === 'undefined') {
                    text('minuteTrafficChart', 'Chart library not loaded.');
                    text('hourlyLoadChart', 'Chart library not loaded.');
                    return;
                }

                if (!minuteTrafficChart) {
                    minuteTrafficChart = new ApexCharts(document.querySelector('#minuteTrafficChart'), {
                        chart: {
                            type: 'line',
                            height: 320,
                            toolbar: { show: true },
                            foreColor: '#425f82'
                        },
                        series: [
                            { name: 'Requests', data: minuteRequests },
                            { name: 'API Requests', data: minuteApiRequests },
                            { name: 'Avg Response (ms)', data: minuteAvgResponse }
                        ],
                        xaxis: { categories: minuteLabels },
                        yaxis: [
                            { title: { text: 'Requests' } },
                            { opposite: true, title: { text: 'Response (ms)' } }
                        ],
                        stroke: { width: [3, 3, 3], curve: 'smooth' },
                        colors: ['#0b72ff', '#16a34a', '#ea580c'],
                        dataLabels: { enabled: false },
                        legend: { position: 'top' },
                        theme: { mode: 'light' },
                    });
                    minuteTrafficChart.render();
                } else {
                    minuteTrafficChart.updateOptions({ xaxis: { categories: minuteLabels } });
                    minuteTrafficChart.updateSeries([
                        { name: 'Requests', data: minuteRequests },
                        { name: 'API Requests', data: minuteApiRequests },
                        { name: 'Avg Response (ms)', data: minuteAvgResponse }
                    ]);
                }

                if (!hourlyLoadChart) {
                    hourlyLoadChart = new ApexCharts(document.querySelector('#hourlyLoadChart'), {
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: { show: true },
                            foreColor: '#425f82'
                        },
                        series: [{ name: 'Hourly Requests', data: hourRequests }],
                        xaxis: { categories: hourLabels },
                        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                        colors: ['#2563eb'],
                        dataLabels: { enabled: false },
                        theme: { mode: 'light' },
                    });
                    hourlyLoadChart.render();
                } else {
                    hourlyLoadChart.updateOptions({ xaxis: { categories: hourLabels } });
                    hourlyLoadChart.updateSeries([{ name: 'Hourly Requests', data: hourRequests }]);
                }
            }

            function renderSnapshot(snapshot) {
                const traffic = snapshot.traffic || {};
                const database = snapshot.database || {};
                const server = snapshot.server || {};
                const container = snapshot.container || {};
                const routes = snapshot.routes || {};

                text('updatedAtText', snapshot.generated_at || '-');
                text('startedAtText', snapshot.started_at || '-');

                text('rpmValue', numberFormat(traffic.responses_per_minute));
                text('rphValue', numberFormat(traffic.responses_per_hour));
                text('avg1mValue', `${numberFormat(traffic.avg_response_ms_1m)} ms`);
                text('errorRateValue', `${numberFormat(traffic.error_rate_1h)}%`);

                updateStatusBadge(database.status);
                text('dbLatencyText', database.latency_ms !== null ? `${numberFormat(database.latency_ms)} ms (${database.connection})` : (database.error || '-'));
                text('apiTrafficText', `${numberFormat(traffic.api_responses_per_minute)} / ${numberFormat(traffic.api_responses_per_hour)}`);
                text('webTrafficText', `${numberFormat(traffic.web_responses_per_minute)} / ${numberFormat(traffic.web_responses_per_hour)}`);

                const loadText = [server.load_1m, server.load_5m, server.load_15m]
                    .map((value) => value === null ? '-' : numberFormat(value))
                    .join(' / ');
                text('serverLoadText', loadText);
                text('serverMemoryText', `${numberFormat(server.memory_usage_mb)} MB / ${numberFormat(server.memory_peak_mb)} MB`);
                text('containerText', `${container.runtime || '-'}${container.in_container ? ' (Container)' : ''}`);
                text('routesText', `${numberFormat(routes.total)} / ${numberFormat(routes.api)} / ${numberFormat(routes.web)}`);

                renderCharts(snapshot);
            }

            async function refreshSnapshot() {
                try {
                    const response = await fetch(snapshotUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    renderSnapshot(payload);
                } catch (error) {
                    console.error('Failed to refresh system health snapshot.', error);
                }
            }

            refreshButton?.addEventListener('click', refreshSnapshot);
            renderSnapshot(initialSnapshot);
            setInterval(refreshSnapshot, 30000);
        });
    </script>
@endsection
