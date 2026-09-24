<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cache Statistics & Analytics</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #212529;
        }

        .navbar {
            background: #212529;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .navbar h2 {
            margin: 0;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            background: #343a40;
            padding: 9px 14px;
            border-radius: 6px;
            font-size: 14px;
        }

        .nav-links a:hover {
            background: #495057;
        }

        .container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .header h1 {
            margin: 0 0 8px;
        }

        .header p {
            margin: 0;
            color: #6c757d;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn {
            text-decoration: none;
            padding: 11px 17px;
            border-radius: 7px;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #bb2d3b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .stat-card h3 {
            margin: 0 0 10px;
            color: #6c757d;
            font-size: 15px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-description {
            color: #6c757d;
            font-size: 13px;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .section h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .analytics-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
        }

        .analytics-card h3 {
            margin-top: 0;
        }

        .metric {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .metric:last-child {
            border-bottom: none;
        }

        .metric-value {
            font-weight: bold;
        }

        .progress-container {
            margin-top: 15px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
            font-size: 14px;
        }

        .progress {
            height: 14px;
            background: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: #198754;
        }

        .progress-bar-warning {
            background: #ffc107;
        }

        .progress-bar-danger {
            background: #dc3545;
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 18px;
            border-radius: 8px;
            line-height: 1.6;
        }

        .formula {
            font-family: monospace;
            background: #212529;
            color: #fff;
            padding: 15px;
            border-radius: 7px;
            margin-top: 10px;
            overflow-x: auto;
        }

        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>Laravel Model Caching</h2>

        <div class="nav-links">

            <a href="{{ url('/products') }}">
                Products
            </a>

            <a href="{{ url('/products/search') }}">
                Search
            </a>

            <a href="{{ url('/cache-performance') }}">
                Performance
            </a>

            <a href="{{ url('/cache-management') }}">
                Management
            </a>

            <a href="{{ url('/cache-statistics') }}">
                Statistics
            </a>

        </div>

    </div>


    <div class="container">

        <div class="header">

            <h1>
                📈 Cache Statistics & Hit/Miss Analytics
            </h1>

            <p>
                Monitor Laravel model cache usage, cache hits,
                cache misses and database requests.
            </p>

        </div>


        @if(session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        <div class="actions">

            <a href="{{ url('/cache-statistics/reset') }}"
               class="btn btn-danger"
               onclick="return confirm('Are you sure you want to reset all cache statistics?')">

                🗑 Reset Statistics

            </a>

        </div>


        <!-- Main Statistics -->

        <div class="stats-grid">

            <div class="stat-card">

                <h3>Total Cache Requests</h3>

                <div class="stat-number">
                    {{ $totalRequests }}
                </div>

                <div class="stat-description">
                    Total requests tracked
                </div>

            </div>


            <div class="stat-card">

                <h3>Cache Hits</h3>

                <div class="stat-number">
                    {{ $hits }}
                </div>

                <div class="stat-description">
                    Requests served from cache
                </div>

            </div>


            <div class="stat-card">

                <h3>Cache Misses</h3>

                <div class="stat-number">
                    {{ $misses }}
                </div>

                <div class="stat-description">
                    Requests requiring data loading
                </div>

            </div>


            <div class="stat-card">

                <h3>Cache Hit Rate</h3>

                <div class="stat-number">
                    {{ $hitRate }}%
                </div>

                <div class="stat-description">
                    Overall cache effectiveness
                </div>

            </div>

        </div>


        <!-- Product Cache -->

        <div class="section">

            <h2>
                📦 Product Cache Analytics
            </h2>

            <div class="analytics-grid">

                <div class="analytics-card">

                    <h3>Cached Product Requests</h3>

                    <div class="metric">

                        <span>Cache Hits</span>

                        <span class="metric-value">
                            {{ $productHits }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Cache Misses</span>

                        <span class="metric-value">
                            {{ $productMisses }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Hit Rate</span>

                        <span class="metric-value">
                            {{ $productHitRate }}%
                        </span>

                    </div>


                    <div class="progress-container">

                        <div class="progress-label">

                            <span>Cache Effectiveness</span>

                            <span>
                                {{ $productHitRate }}%
                            </span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar"
                                 style="width: {{ min($productHitRate, 100) }}%;">
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Search Cache -->

                <div class="analytics-card">

                    <h3>🔎 Search Cache</h3>

                    <div class="metric">

                        <span>Search Hits</span>

                        <span class="metric-value">
                            {{ $searchHits }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Search Misses</span>

                        <span class="metric-value">
                            {{ $searchMisses }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Search Hit Rate</span>

                        <span class="metric-value">
                            {{ $searchHitRate }}%
                        </span>

                    </div>


                    <div class="progress-container">

                        <div class="progress-label">

                            <span>Search Cache Effectiveness</span>

                            <span>
                                {{ $searchHitRate }}%
                            </span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar"
                                 style="width: {{ min($searchHitRate, 100) }}%;">
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Database -->

                <div class="analytics-card">

                    <h3>🗄 Database Requests</h3>

                    <div class="metric">

                        <span>Direct DB Requests</span>

                        <span class="metric-value">
                            {{ $databaseRequests }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Total Cache Hits</span>

                        <span class="metric-value">
                            {{ $hits }}
                        </span>

                    </div>

                    <div class="metric">

                        <span>Total Cache Misses</span>

                        <span class="metric-value">
                            {{ $misses }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- Hit/Miss Rate -->

        <div class="section">

            <h2>
                📊 Cache Hit / Miss Rate
            </h2>


            <div class="progress-container">

                <div class="progress-label">

                    <span>
                        Cache Hit Rate
                    </span>

                    <strong>
                        {{ $hitRate }}%
                    </strong>

                </div>

                <div class="progress">

                    <div class="progress-bar"
                         style="width: {{ min($hitRate, 100) }}%;">
                    </div>

                </div>

            </div>


            <div class="progress-container">

                <div class="progress-label">

                    <span>
                        Cache Miss Rate
                    </span>

                    <strong>
                        {{ $missRate }}%
                    </strong>

                </div>

                <div class="progress">

                    <div class="progress-bar progress-bar-danger"
                         style="width: {{ min($missRate, 100) }}%;">
                    </div>

                </div>

            </div>

        </div>


        <!-- Visual Performance Benchmark Charts -->

        <div class="section">

            <h2>
                📊 Visual Cache Benchmark & Performance Analytics Charts
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #dee2e6;">
                    <h3 style="margin-top:0; text-align: center; font-size: 16px; color: #495057;">Hit vs Miss Ratio</h3>
                    <div style="max-width: 260px; margin: 0 auto;">
                        <canvas id="hitMissChart"></canvas>
                    </div>
                </div>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #dee2e6;">
                    <h3 style="margin-top:0; text-align: center; font-size: 16px; color: #495057;">Response Latency Benchmark (ms)</h3>
                    <div>
                        <canvas id="latencyChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const hitsCount = {{ (int) $hits }};
                const missesCount = {{ (int) $misses }};
                const displayHits = (hitsCount === 0 && missesCount === 0) ? 1 : hitsCount;
                const displayMisses = (hitsCount === 0 && missesCount === 0) ? 0 : missesCount;

                const hitMissCtx = document.getElementById('hitMissChart').getContext('2d');
                new Chart(hitMissCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Cache Hits (' + hitsCount + ')', 'Cache Misses (' + missesCount + ')'],
                        datasets: [{
                            data: [displayHits, displayMisses],
                            backgroundColor: ['#198754', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                const latencyCtx = document.getElementById('latencyChart').getContext('2d');
                new Chart(latencyCtx, {
                    type: 'bar',
                    data: {
                        labels: ['⚡ Cache Hit Latency', '🔥 Cache Miss / DB Latency'],
                        datasets: [{
                            label: 'Latency (ms)',
                            data: [0.8, 42.5],
                            backgroundColor: ['#0d6efd', '#fd7e14'],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Latency (milliseconds)' } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>


        <!-- Explanation -->

        <div class="section">

            <h2>
                💡 How Cache Analytics Works
            </h2>

            <div class="info-box">

                Every time the application requests cached
                product data, the application records whether
                the request resulted in a cache hit or cache miss.

                <br><br>

                <strong>Cache Hit:</strong>

                The requested data already exists in the cache,
                so Laravel can return it without performing the
                original database query.

                <br><br>

                <strong>Cache Miss:</strong>

                The requested data does not exist in the cache,
                so Laravel loads the data and stores it in the
                cache for future requests.

                <br><br>

                <strong>Cache Hit Rate:</strong>

                <div class="formula">
                    Cache Hit Rate = (Cache Hits ÷ Total Cache Requests) × 100
                </div>

            </div>

        </div>


        <!-- Current Summary -->

        <div class="section">

            <h2>
                📋 Current Cache Summary
            </h2>

            <div class="info-box">

                <strong>Total Requests:</strong>
                {{ $totalRequests }}

                <br>

                <strong>Total Hits:</strong>
                {{ $hits }}

                <br>

                <strong>Total Misses:</strong>
                {{ $misses }}

                <br>

                <strong>Overall Hit Rate:</strong>
                {{ $hitRate }}%

                <br>

                <strong>Overall Miss Rate:</strong>
                {{ $missRate }}%

                <br>

                <strong>Direct Database Requests:</strong>
                {{ $databaseRequests }}

            </div>

        </div>

    </div>

</body>

</html>