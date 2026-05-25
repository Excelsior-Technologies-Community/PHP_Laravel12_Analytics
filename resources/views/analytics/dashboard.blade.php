<!DOCTYPE html>
<html lang="en" id="html-root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #f4f5f7;
            --surface: #ffffff;
            --surface2: #f9fafb;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --accent: #4f46e5;
            --accent-soft: #eef2ff;
            --green: #059669;
            --green-soft: #ecfdf5;
            --purple: #7c3aed;
            --purple-soft: #f5f3ff;
            --red: #dc2626;
            --red-soft: #fef2f2;
            --live-dot: #22c55e;
        }
        .dark {
            --bg: #0f1117;
            --surface: #1a1d27;
            --surface2: #22263a;
            --border: #2d3348;
            --text: #f1f5f9;
            --muted: #8b95b0;
            --accent: #6366f1;
            --accent-soft: #1e1b4b;
            --green: #10b981;
            --green-soft: #064e3b;
            --purple: #a78bfa;
            --purple-soft: #2e1065;
            --red: #f87171;
            --red-soft: #450a0a;
            --live-dot: #4ade80;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background 0.2s, color 0.2s;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: background 0.2s, border-color 0.2s;
        }
        .metric-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            transition: background 0.2s, border-color 0.2s;
        }
        .metric-label {
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: 600;
            line-height: 1;
            font-family: 'DM Mono', monospace;
        }
        /* Live pulse dot */
        .live-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            background: var(--live-dot);
            display: inline-block;
            margin-right: 6px;
            position: relative;
            vertical-align: middle;
        }
        .live-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: var(--live-dot);
            opacity: 0.3;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%   { transform: scale(1); opacity: 0.3; }
            70%  { transform: scale(2); opacity: 0; }
            100% { transform: scale(1); opacity: 0; }
        }
        /* Tab Buttons */
        .tab-btn {
            padding: 0.4rem 1rem;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            background: transparent;
            color: var(--muted);
            transition: all 0.15s;
        }
        .tab-btn.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .tab-btn:not(.active):hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--border);
        }
        /* Form elements */
        select, input[type="date"], input[type="text"] {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.45rem 0.75rem;
            font-size: 14px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.15s, background 0.2s;
            width: 100%;
        }
        select:focus, input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.45rem 1.25rem;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-export {
            background: var(--green-soft);
            color: var(--green);
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 0.45rem 1.1rem;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: opacity 0.15s;
            display: inline-block;
        }
        .btn-export:hover { opacity: 0.8; }
        /* Dark mode toggle */
        .toggle-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }
        .toggle-track {
            width: 42px; height: 24px;
            border-radius: 12px;
            background: var(--border);
            position: relative;
            transition: background 0.2s;
        }
        .toggle-track.on { background: var(--accent); }
        .toggle-thumb {
            width: 18px; height: 18px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: 3px; left: 3px;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(18px); }
        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }
        td {
            padding: 0.7rem 0.75rem;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-family: 'DM Mono', monospace;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--surface2); }
        /* Page item */
        .page-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            margin-bottom: 0.5rem;
            transition: background 0.15s;
        }
        .page-item:hover { background: var(--accent-soft); }
        .page-url { font-size: 13px; font-family: 'DM Mono', monospace; color: var(--text); }
        .page-views {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 2px 10px;
            border-radius: 6px;
            font-family: 'DM Mono', monospace;
        }
        /* Realtime card */
        .realtime-card {
            background: linear-gradient(135deg, var(--accent-soft), var(--surface));
            border: 1px solid var(--accent);
        }
        .realtime-number {
            font-size: 3rem;
            font-weight: 600;
            font-family: 'DM Mono', monospace;
            color: var(--accent);
            line-height: 1;
        }
        /* Quick range pill */
        .range-pill {
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            transition: all 0.15s;
        }
        .range-pill.active, .range-pill:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        /* Hidden */
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        @media (max-width: 640px) {
            .realtime-number { font-size: 2rem; }
        }
    </style>
</head>

<body id="body-root">
<div style="max-width: 1100px; margin: 0 auto; padding: 1.5rem 1rem;">

    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 600; color: var(--text); margin-bottom: 4px;">Analytics Dashboard</h1>
            <p style="font-size: 14px; color: var(--muted);">Laravel Analytics Report</p>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Dark Mode Toggle -->
            <label class="toggle-wrap" for="darkToggle" title="Toggle dark mode">
                <span style="font-size: 18px;">☀️</span>
                <div class="toggle-track" id="toggleTrack">
                    <div class="toggle-thumb"></div>
                </div>
                <span style="font-size: 18px;">🌙</span>
            </label>
            <input type="checkbox" id="darkToggle" style="display:none;">
            <a href="/analytics/export" class="btn-export">↓ Export CSV</a>
        </div>
    </div>

    <!-- REALTIME + QUICK FILTERS ROW -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1.5rem;" class="realtime-row">

        <!-- REALTIME VISITORS CARD -->
        <div class="card realtime-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span class="live-dot"></span>
                <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--green);">Live Right Now</span>
            </div>
            <div class="realtime-number" id="realtimeCount">--</div>
            <p style="font-size: 13px; color: var(--muted);">Active users on site</p>
            <p style="font-size: 11px; color: var(--muted);" id="realtimeUpdated">Updating every 10s...</p>
        </div>

        <!-- DATE RANGE PICKER CARD -->
        <div class="card" style="padding: 1.25rem;">
            <p style="font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.75rem;">Date Range Filter</p>

            <!-- Quick Presets -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1rem;">
                <button class="range-pill active" onclick="setRange(7, this)">Last 7 days</button>
                <button class="range-pill" onclick="setRange(14, this)">Last 14 days</button>
                <button class="range-pill" onclick="setRange(30, this)">Last 30 days</button>
                <button class="range-pill" onclick="setRange(90, this)">Last 90 days</button>
            </div>

            <!-- Custom Range -->
            <form method="GET" action="/analytics">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 8px; align-items: end;">
                    <div>
                        <label style="font-size: 11px; color: var(--muted); display: block; margin-bottom: 4px;">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" id="dateFrom">
                    </div>
                    <div>
                        <label style="font-size: 11px; color: var(--muted); display: block; margin-bottom: 4px;">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" id="dateTo">
                    </div>
                    <div>
                        <label style="font-size: 11px; color: var(--muted); display: block; margin-bottom: 4px;">Search URL</label>
                        <input type="text" name="search" placeholder="e.g. /about" value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-primary">Apply</button>
                </div>
                <input type="hidden" name="days" id="hiddenDays" value="{{ request('days', 7) }}">
            </form>
        </div>
    </div>

    <!-- METRIC CARDS -->
    @php
        $totalVisitors = collect($visitors)->sum('visitors');
        $totalPageViews = collect($visitors)->sum('pageViews');
        $avgVisitors = count($visitors) ? round($totalVisitors / count($visitors)) : 0;
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="metric-card">
            <div class="metric-label">Total Visitors</div>
            <div class="metric-value" style="color: var(--accent);">{{ number_format($totalVisitors) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total Page Views</div>
            <div class="metric-value" style="color: var(--green);">{{ number_format($totalPageViews) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Avg / Day</div>
            <div class="metric-value" style="color: var(--purple);">{{ number_format($avgVisitors) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Active Days</div>
            <div class="metric-value" style="color: var(--muted);">{{ count($visitors) }}</div>
        </div>
    </div>

    <!-- TABS -->
    <div style="display: flex; gap: 6px; margin-bottom: 1rem; flex-wrap: wrap;">
        <button class="tab-btn active" onclick="showTab('overview', this)">Overview</button>
        <button class="tab-btn" onclick="showTab('visitors', this)">Visitors</button>
        <button class="tab-btn" onclick="showTab('pages', this)">Top Pages</button>
        <button class="tab-btn" onclick="showTab('chartTab', this)">Chart</button>
    </div>

    <!-- OVERVIEW TAB -->
    <div id="overview" class="tab-content active card" style="padding: 1.5rem;">
        <h2 style="font-size: 15px; font-weight: 600; margin-bottom: 1rem; color: var(--text);">Summary</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="background: var(--accent-soft); border-radius: 10px; padding: 1rem;">
                <div style="font-size: 12px; color: var(--accent); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em;">Peak Day</div>
                @php
                    $peak = collect($visitors)->sortByDesc('visitors')->first();
                @endphp
                <div style="font-size: 1.25rem; font-weight: 600; font-family: 'DM Mono', monospace; color: var(--accent);">{{ $peak['visitors'] ?? '--' }} visitors</div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">{{ $peak['date'] ?? '' }}</div>
            </div>
            <div style="background: var(--green-soft); border-radius: 10px; padding: 1rem;">
                <div style="font-size: 12px; color: var(--green); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em;">Views/Visitor</div>
                <div style="font-size: 1.25rem; font-weight: 600; font-family: 'DM Mono', monospace; color: var(--green);">
                    {{ $totalVisitors ? number_format($totalPageViews / $totalVisitors, 1) : '0' }}x
                </div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Engagement ratio</div>
            </div>
        </div>
    </div>

    <!-- VISITORS TAB -->
    <div id="visitors" class="tab-content card" style="padding: 1.5rem;">
        <h2 style="font-size: 15px; font-weight: 600; margin-bottom: 1rem; color: var(--text);">Daily Visitors</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Visitors</th>
                        <th>Page Views</th>
                        <th>Ratio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visitors as $v)
                    <tr>
                        <td>{{ $v['date'] }}</td>
                        <td>{{ number_format($v['visitors']) }}</td>
                        <td>{{ number_format($v['pageViews']) }}</td>
                        <td>{{ $v['visitors'] ? number_format($v['pageViews'] / $v['visitors'], 1) : '0' }}x</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TOP PAGES TAB -->
    <div id="pages" class="tab-content card" style="padding: 1.5rem;">
        <h2 style="font-size: 15px; font-weight: 600; margin-bottom: 1rem; color: var(--text);">Top Pages</h2>
        @foreach($topPages as $page)
        <div class="page-item">
            <span class="page-url">{{ $page['url'] }}</span>
            <span class="page-views">{{ number_format($page['pageViews']) }} views</span>
        </div>
        @endforeach
    </div>

    <!-- CHART TAB -->
    <div id="chartTab" class="tab-content card" style="padding: 1.5rem;">
        <h2 style="font-size: 15px; font-weight: 600; margin-bottom: 1rem; color: var(--text);">Visitors Trend</h2>
        <canvas id="chart" style="max-height: 320px;"></canvas>
    </div>

</div>

<script>
    // ── Dark Mode ──────────────────────────────────────────────
    const htmlRoot = document.getElementById('html-root');
    const toggleTrack = document.getElementById('toggleTrack');
    const darkToggle = document.getElementById('darkToggle');

    const savedTheme = localStorage.getItem('analytics_theme');
    if (savedTheme === 'dark') applyDark(true);

    document.querySelector('.toggle-wrap').addEventListener('click', () => {
        const isDark = htmlRoot.classList.contains('dark');
        applyDark(!isDark);
        localStorage.setItem('analytics_theme', isDark ? 'light' : 'dark');
    });

    function applyDark(on) {
        htmlRoot.classList.toggle('dark', on);
        toggleTrack.classList.toggle('on', on);
        if (chartInstance) updateChartTheme();
    }

    // ── Tabs ──────────────────────────────────────────────────
    function showTab(id, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        btn.classList.add('active');
    }

    // ── Date Range Presets ────────────────────────────────────
    function setRange(days, btn) {
        document.querySelectorAll('.range-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('hiddenDays').value = days;

        const to = new Date();
        const from = new Date();
        from.setDate(from.getDate() - (days - 1));
        document.getElementById('dateFrom').value = from.toISOString().split('T')[0];
        document.getElementById('dateTo').value = to.toISOString().split('T')[0];
    }

    // ── Real-time Visitors ────────────────────────────────────
    function updateRealtime() {
        // Simulated — replace with your real endpoint:
        // fetch('/analytics/realtime').then(r => r.json()).then(d => ...)
        const fakeCount = Math.floor(Math.random() * 40) + 5;
        document.getElementById('realtimeCount').textContent = fakeCount;
        const now = new Date();
        document.getElementById('realtimeUpdated').textContent =
            'Updated at ' + now.toLocaleTimeString();
    }

    updateRealtime();
    setInterval(updateRealtime, 10000);

    // ── Chart ─────────────────────────────────────────────────
    const visitorData = @json($visitors);
    let chartInstance = null;

    function getChartColors() {
        const isDark = document.getElementById('html-root').classList.contains('dark');
        return {
            accent:    isDark ? '#6366f1' : '#4f46e5',
            accentBg:  isDark ? 'rgba(99,102,241,0.15)' : 'rgba(79,70,229,0.1)',
            grid:      isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
            label:     isDark ? '#8b95b0' : '#6b7280',
        };
    }

    function updateChartTheme() {
        if (!chartInstance) return;
        const c = getChartColors();
        chartInstance.data.datasets[0].borderColor = c.accent;
        chartInstance.data.datasets[0].backgroundColor = c.accentBg;
        chartInstance.data.datasets[0].pointBackgroundColor = c.accent;
        chartInstance.options.scales.x.grid.color = c.grid;
        chartInstance.options.scales.y.grid.color = c.grid;
        chartInstance.options.scales.x.ticks.color = c.label;
        chartInstance.options.scales.y.ticks.color = c.label;
        chartInstance.update();
    }

    const c = getChartColors();
    chartInstance = new Chart(document.getElementById('chart'), {
        type: 'line',
        data: {
            labels: visitorData.map(i => i.date),
            datasets: [{
                label: 'Visitors',
                data: visitorData.map(i => i.visitors),
                borderColor: c.accent,
                backgroundColor: c.accentBg,
                pointBackgroundColor: c.accent,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { color: c.grid },
                    ticks: { color: c.label, font: { family: 'DM Mono', size: 11 } }
                },
                y: {
                    grid: { color: c.grid },
                    ticks: { color: c.label, font: { family: 'DM Mono', size: 11 } }
                }
            }
        }
    });
</script>
</body>
</html>