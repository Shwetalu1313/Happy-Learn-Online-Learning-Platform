@extends('admin.layouts.app')

@section('content')
    <style>
        .fx-shell {
            --fx-bg-a: #f8fafc;
            --fx-bg-b: #eef2ff;
            --fx-accent: #0ea5e9;
            --fx-accent-2: #f59e0b;
            --fx-soft: rgba(255, 255, 255, 0.72);
            background: radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.18), transparent 35%),
                        radial-gradient(circle at 85% 80%, rgba(245, 158, 11, 0.18), transparent 35%),
                        linear-gradient(140deg, var(--fx-bg-a), var(--fx-bg-b));
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            padding: 1.25rem;
        }

        .fx-title {
            color: #0f172a;
            letter-spacing: 0.6px;
            margin: 0;
        }

        .fx-sub {
            color: #334155;
            margin: 0;
        }

        .fx-panel {
            background: var(--fx-soft);
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 14px;
            backdrop-filter: blur(6px);
            padding: 1rem;
        }

        .fx-chart-wrap {
            height: 30vh;
            min-height: 220px;
            max-height: 340px;
        }

        .fx-chip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
            gap: 0.45rem;
        }

        .fx-chip {
            border: 1px solid rgba(148, 163, 184, 0.45);
            border-radius: 999px;
            padding: 0.35rem 0.55rem;
            color: #0f172a;
            background: rgba(255, 255, 255, 0.9);
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
        }

        .fx-chip input {
            accent-color: var(--fx-accent);
        }

        .fx-note {
            color: #0c4a6e;
            font-size: 0.9rem;
            background: rgba(186, 230, 253, 0.55);
            border: 1px solid rgba(14, 165, 233, 0.35);
            border-radius: 10px;
            padding: 0.5rem 0.65rem;
        }

        .fx-label {
            color: #1e293b;
            font-weight: 600;
        }

        .fx-meta {
            color: #334155;
        }
    </style>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-bag-x me-3"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2-circle text-success me-3"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="fx-shell mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h3 class="fx-title">World Live Currency Line Chart</h3>
                <p class="fx-sub">Base currency is fixed to MMK</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="liveStatus" class="badge text-bg-secondary">Loading...</span>
                <span class="badge" style="background: rgba(14,165,233,0.12); color: #075985;">Base: MMK</span>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="historyMonths" class="form-label fx-label mb-1">Chart Range</label>
                <select id="historyMonths" class="form-select">
                    <option value="1">1 month</option>
                    <option value="3">3 months</option>
                    <option value="5" selected>5 months</option>
                    <option value="8">8 months</option>
                    <option value="12">12 months</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="pollSeconds" class="form-label fx-label mb-1">Live Update Interval</label>
                <select id="pollSeconds" class="form-select">
                    <option value="30" selected>30 seconds</option>
                    <option value="60">60 seconds</option>
                    <option value="120">120 seconds</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="fx-note mt-md-4">
                    External market data only. Updating USD/PTS below does not change this chart.
                </div>
            </div>
        </div>

        <div class="fx-panel mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fx-label m-0">Track Quote Currencies</label>
                <button id="refreshChart" type="button" class="btn btn-sm btn-outline-primary">Refresh Now</button>
            </div>
            <div id="quoteCurrencies" class="fx-chip-grid"></div>
            <small id="lastUpdated" class="fx-meta d-block mt-2">Preparing live feed...</small>
        </div>

        <div class="fx-panel">
            <div class="fx-chart-wrap">
                <canvas id="worldCurrencyChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h3 class="card-title text-primary-emphasis">Exchange Rate Modify</h3>
        <form action="{{ route('usUpdate') }}" method="POST" class="mb-5 shadow-sm p-5 d-flex flex-column justify-content-center">
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

        <form action="{{ route('ptsUpdate') }}" method="POST" class="mb-5 shadow-sm p-5 d-flex flex-column justify-content-center">
            @method('PUT')
            @csrf
            <div class="input-group mb-3 ">
                <label for="pts_ex" class="col-sm-2 col-form-label">
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
@endsection

@section('scripts')
    <script>
        (() => {
            const FX_API_BASE = "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api";
            const BASE_CODE = "MMK";
            const SOURCE_BASE_CODE = "USD";
            const DEFAULT_QUOTES = ["USD", "EUR", "GBP", "JPY", "AUD", "CAD", "SGD", "INR", "CNY", "THB"];
            const COLORS = ["#0ea5e9", "#f97316", "#16a34a", "#2563eb", "#e11d48", "#ca8a04", "#7c3aed", "#0f766e", "#be123c", "#0284c7"];
            const FETCH_BATCH_SIZE = 10;

            const liveStatusEl = document.getElementById("liveStatus");
            const lastUpdatedEl = document.getElementById("lastUpdated");
            const quoteCurrenciesEl = document.getElementById("quoteCurrencies");
            const refreshChartEl = document.getElementById("refreshChart");
            const pollSecondsEl = document.getElementById("pollSeconds");
            const historyMonthsEl = document.getElementById("historyMonths");
            const chartCanvas = document.getElementById("worldCurrencyChart");

            let chartInstance = null;
            let pollTimer = null;
            let latestPayload = null;
            const historyCache = new Map();

            function setStatus(text, type = "secondary") {
                liveStatusEl.className = `badge text-bg-${type}`;
                liveStatusEl.textContent = text;
            }

            function nowLabel() {
                return new Date().toLocaleTimeString();
            }

            function selectedQuotes() {
                return Array.from(quoteCurrenciesEl.querySelectorAll("input[type='checkbox']:checked"))
                    .map((el) => el.value);
            }

            function toDateKey(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, "0");
                const d = String(date.getDate()).padStart(2, "0");
                return `${y}-${m}-${d}`;
            }

            function isWeekday(date) {
                const day = date.getDay();
                return day !== 0 && day !== 6;
            }

            function getDateRangeByMonths(months) {
                const end = new Date();
                end.setHours(0, 0, 0, 0);
                const start = new Date(end);
                start.setMonth(start.getMonth() - months);

                const days = [];
                const current = new Date(start);
                while (current <= end) {
                    if (isWeekday(current)) {
                        days.push(new Date(current));
                    }
                    current.setDate(current.getDate() + 1);
                }
                return days;
            }

            function buildQuoteControls(rates) {
                quoteCurrenciesEl.innerHTML = "";
                const codes = DEFAULT_QUOTES.filter((code) => code !== BASE_CODE && Object.prototype.hasOwnProperty.call(rates, code.toLowerCase()));

                codes.forEach((code, idx) => {
                    const label = document.createElement("label");
                    label.className = "fx-chip";

                    const input = document.createElement("input");
                    input.type = "checkbox";
                    input.value = code;
                    input.checked = idx < 5;

                    const text = document.createElement("span");
                    text.textContent = code;

                    label.appendChild(input);
                    label.appendChild(text);
                    quoteCurrenciesEl.appendChild(label);
                });
            }

            function makeDatasets(quotes) {
                return quotes.map((code, idx) => ({
                    label: code,
                    borderColor: COLORS[idx % COLORS.length],
                    backgroundColor: COLORS[idx % COLORS.length],
                    borderWidth: 2,
                    tension: 0.28,
                    fill: false,
                    data: [],
                }));
            }

            function renderChart(labels, quotes, dataByCode) {
                if (quotes.length === 0) {
                    setStatus("Select currencies", "warning");
                    return;
                }

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(chartCanvas.getContext("2d"), {
                    type: "line",
                    data: {
                        labels,
                        datasets: makeDatasets(quotes).map((dataset, idx) => ({
                            ...dataset,
                            borderColor: COLORS[idx % COLORS.length],
                            backgroundColor: COLORS[idx % COLORS.length],
                            data: dataByCode[dataset.label] || [],
                        })),
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: "index", intersect: false },
                        plugins: {
                            legend: { position: "bottom", labels: { color: "#0f172a", boxWidth: 20 } },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${context.parsed.y}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: { color: "#334155", maxTicksLimit: 8 },
                                grid: { color: "rgba(148,163,184,0.25)" },
                            },
                            y: {
                                ticks: { color: "#334155" },
                                grid: { color: "rgba(148,163,184,0.25)" },
                                title: {
                                    display: true,
                                    text: `MMK per 1 unit`,
                                    color: "#334155",
                                },
                            },
                        },
                    },
                });
            }

            function buildSeries(pointsByDate, quoteCodes) {
                const labels = Object.keys(pointsByDate).sort();
                const dataByCode = {};
                quoteCodes.forEach((code) => {
                    dataByCode[code] = labels.map((day) => pointsByDate[day]?.[code] ?? null);
                });
                return { labels, dataByCode };
            }

            async function fetchHistoryPoint(date) {
                const dateKey = toDateKey(date);
                try {
                    const url = `${FX_API_BASE}@${dateKey}/v1/currencies/${SOURCE_BASE_CODE.toLowerCase()}.json`;
                    const response = await fetch(url, { cache: "no-store" });
                    if (!response.ok) {
                        return null;
                    }

                    const payload = await response.json();
                    const sourceRates = payload ? payload[SOURCE_BASE_CODE.toLowerCase()] : null;
                    if (!sourceRates || !sourceRates.mmk) {
                        return null;
                    }

                    const rates = {};
                    DEFAULT_QUOTES.forEach((code) => {
                        const lowerCode = code.toLowerCase();
                        if (!sourceRates[lowerCode]) {
                            rates[code] = null;
                            return;
                        }

                        rates[code] = sourceRates.mmk / sourceRates[lowerCode];
                    });

                    return { dateKey, rates };
                } catch {
                    return null;
                }
            }

            async function loadHistoricalPoints(months) {
                if (historyCache.has(months)) {
                    return historyCache.get(months);
                }

                const dates = getDateRangeByMonths(months);
                const pointsByDate = {};
                let loadedCount = 0;

                for (let i = 0; i < dates.length; i += FETCH_BATCH_SIZE) {
                    const chunk = dates.slice(i, i + FETCH_BATCH_SIZE);
                    const results = await Promise.all(chunk.map(fetchHistoryPoint));
                    results.filter(Boolean).forEach((dayResult) => {
                        pointsByDate[dayResult.dateKey] = dayResult.rates;
                        loadedCount++;
                    });
                }

                const historicalData = { pointsByDate, loadedCount, requestedCount: dates.length };
                historyCache.set(months, historicalData);
                return historicalData;
            }

            function appendPoint(payload) {
                if (!chartInstance) {
                    return;
                }

                const sourceRates = payload ? payload[SOURCE_BASE_CODE.toLowerCase()] : null;
                if (!sourceRates || !sourceRates.mmk) {
                    return;
                }

                const label = `${new Date().toISOString().slice(0, 10)} ${nowLabel()}`;
                chartInstance.data.labels.push(label);

                chartInstance.data.datasets.forEach((dataset) => {
                    const usdToMmkLive = sourceRates ? sourceRates.mmk : null;
                    const usdToQuote = sourceRates ? sourceRates[dataset.label.toLowerCase()] : null;
                    if (!usdToQuote || !usdToMmkLive) {
                        dataset.data.push(null);
                        return;
                    }

                    dataset.data.push(usdToMmkLive / usdToQuote);
                });

                const maxPoints = 200;
                if (chartInstance.data.labels.length > maxPoints) {
                    chartInstance.data.labels.shift();
                    chartInstance.data.datasets.forEach((dataset) => dataset.data.shift());
                }

                chartInstance.update("none");
                setStatus("Live", "success");
                lastUpdatedEl.textContent = `Last updated: ${new Date().toLocaleString()}`;
            }

            async function fetchLiveSnapshot() {
                const response = await fetch(`${FX_API_BASE}@latest/v1/currencies/${SOURCE_BASE_CODE.toLowerCase()}.json`, { cache: "no-store" });
                if (!response.ok) {
                    throw new Error(`Live API status ${response.status}`);
                }

                const payload = await response.json();
                const sourceRates = payload ? payload[SOURCE_BASE_CODE.toLowerCase()] : null;
                if (!sourceRates || !sourceRates.mmk) {
                    throw new Error("Unexpected API payload");
                }

                return payload;
            }

            async function rebuildChartFromHistory() {
                try {
                    const quoteCodes = selectedQuotes();
                    if (quoteCodes.length === 0) {
                        setStatus("Select currencies", "warning");
                        lastUpdatedEl.textContent = "Select at least one quote currency.";
                        return;
                    }

                    const months = parseInt(historyMonthsEl.value, 10);
                    setStatus("Loading...", "secondary");

                    const historicalData = await loadHistoricalPoints(months);
                    const series = buildSeries(historicalData.pointsByDate, quoteCodes);
                    renderChart(series.labels, quoteCodes, series.dataByCode);
                    lastUpdatedEl.textContent = `History loaded: ${historicalData.loadedCount}/${historicalData.requestedCount} market days`;
                } catch (error) {
                    setStatus("History Error", "danger");
                    lastUpdatedEl.textContent = `Failed to load historical range: ${error.message}`;
                }
            }

            async function refreshNow() {
                try {
                    setStatus("Loading...", "secondary");
                    const payload = await fetchLiveSnapshot();
                    latestPayload = payload;
                    appendPoint(payload);
                } catch (error) {
                    setStatus("Live Error", "danger");
                    lastUpdatedEl.textContent = `Update failed: ${error.message}`;
                }
            }

            async function refreshAll() {
                await rebuildChartFromHistory();
                await refreshNow();
            }

            function stopPolling() {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            }

            function startPolling() {
                stopPolling();
                const seconds = parseInt(pollSecondsEl.value, 10);
                pollTimer = setInterval(refreshNow, seconds * 1000);
            }

            async function init() {
                try {
                    if (typeof Chart === "undefined") {
                        throw new Error("Chart.js not loaded");
                    }

                    setStatus("Loading...", "secondary");
                    const payload = await fetchLiveSnapshot();
                    latestPayload = payload;
                    buildQuoteControls(payload[SOURCE_BASE_CODE.toLowerCase()]);
                    await rebuildChartFromHistory();
                    appendPoint(payload);
                    startPolling();
                    setStatus("Live", "success");
                    lastUpdatedEl.textContent = `Connected: ${new Date().toLocaleString()}`;
                } catch (error) {
                    setStatus("Init Error", "danger");
                    lastUpdatedEl.textContent = `Failed to initialize live chart: ${error.message}`;
                }
            }

            quoteCurrenciesEl.addEventListener("change", refreshAll);
            historyMonthsEl.addEventListener("change", refreshAll);
            refreshChartEl.addEventListener("click", refreshAll);
            pollSecondsEl.addEventListener("change", startPolling);

            init();
        })();
    </script>
@endsection
