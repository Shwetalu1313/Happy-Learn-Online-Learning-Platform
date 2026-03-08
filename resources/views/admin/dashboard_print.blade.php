<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard Report ({{ $paper }})</title>
    @php
        $paperSize = match($paper) {
            'A5' => 'A5 portrait',
            'A3' => 'A3 landscape',
            'A1' => 'A1 landscape',
            default => 'A4 portrait',
        };
    @endphp
    <style>
        @page {
            size: {{ $paperSize }};
            margin: 12mm;
        }

        :root {
            --bg: #f8fafc;
            --panel: #ffffff;
            --line: #dbe7f3;
            --text: #0f172a;
            --muted: #64748b;
            --up: #15803d;
            --down: #b91c1c;
            --accent: #0369a1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--text);
            background: var(--bg);
            font-family: "Segoe UI", Tahoma, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .screen-toolbar {
            max-width: 1500px;
            margin: 16px auto 0;
            padding: 0 10px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .screen-toolbar button {
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            background: #e0f2fe;
            color: #075985;
            font-weight: 600;
            padding: 8px 12px;
            cursor: pointer;
        }

        .report {
            max-width: 1500px;
            margin: 10px auto;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
            padding: 14px;
        }

        .head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .head h1 {
            margin: 0;
            font-size: 20px;
        }

        .meta {
            color: var(--muted);
            margin: 2px 0 0;
        }

        .kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .kpi {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            background: #fbfdff;
        }

        .kpi .label {
            color: var(--muted);
            margin: 0;
            font-size: 11px;
        }

        .kpi .value {
            margin: 4px 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--accent);
        }

        .kpi .detail {
            margin: 0;
            font-size: 11px;
            color: var(--muted);
        }

        .up {
            color: var(--up);
            font-weight: 700;
        }

        .down {
            color: var(--down);
            font-weight: 700;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
            font-weight: 700;
        }

        .text-end {
            text-align: right;
        }

        .foot {
            margin-top: 10px;
            color: var(--muted);
            font-size: 10px;
        }

        @media print {
            body {
                background: #fff;
            }

            .screen-toolbar {
                display: none;
            }

            .report {
                margin: 0;
                border: 0;
                border-radius: 0;
                padding: 0;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <button type="button" onclick="window.print()">Print {{ $paper }}</button>
    </div>

    <main class="report">
        <header class="head">
            <div>
                <h1>Admin Dashboard Report</h1>
                <p class="meta">Paper: {{ $paper }} | Timeline: Last {{ $days }} days</p>
            </div>
            <div>
                <p class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</p>
                <p class="meta">System timezone: {{ config('app.timezone') }}</p>
            </div>
        </header>

        <section class="kpis">
            <article class="kpi">
                <p class="label">Enrollments (Current Month)</p>
                <p class="value">{{ number_format($metrics['enrollments']['current']) }}</p>
                <p class="detail">
                    Previous: {{ number_format($metrics['enrollments']['previous']) }} |
                    <span class="{{ $metrics['enrollments']['trend'] === 'up' ? 'up' : 'down' }}">
                        {{ number_format($metrics['enrollments']['change'], 2) }}%
                    </span>
                </p>
            </article>
            <article class="kpi">
                <p class="label">Income (Current Month, MMK)</p>
                <p class="value">{{ number_format($metrics['income']['current_mmk']) }}</p>
                <p class="detail">
                    Previous: {{ number_format($metrics['income']['previous_mmk']) }} |
                    <span class="{{ $metrics['income']['trend'] === 'up' ? 'up' : 'down' }}">
                        {{ number_format($metrics['income']['change'], 2) }}%
                    </span>
                </p>
            </article>
            <article class="kpi">
                <p class="label">Registrations (Current Month)</p>
                <p class="value">{{ number_format($metrics['registrations']['current']) }}</p>
                <p class="detail">
                    Previous: {{ number_format($metrics['registrations']['previous']) }} |
                    <span class="{{ $metrics['registrations']['trend'] === 'up' ? 'up' : 'down' }}">
                        {{ number_format($metrics['registrations']['change'], 2) }}%
                    </span>
                </p>
            </article>
        </section>

        <section>
            <h2 class="section-title">Daily Timeline (Last {{ $days }} Days)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Enrollments</th>
                        <th class="text-end">Income (MMK)</th>
                        <th class="text-end">Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timelineRows as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td class="text-end">{{ number_format($row['enrollments']) }}</td>
                            <td class="text-end">{{ number_format($row['income']) }}</td>
                            <td class="text-end">{{ number_format($row['registrations']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No data found for selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <p class="foot">
            This report uses the same dashboard formulas for month-over-month KPIs and timeline aggregations.
        </p>
    </main>
</body>
</html>
