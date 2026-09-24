<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Cache Management</title>

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
            padding: 15px 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        .navbar a:hover {
            background: #343a40;
        }

        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .header,
        .card,
        .keys {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .header h1 {
            margin-top: 0;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        .active {
            background: #d1e7dd;
            color: #0f5132;
        }

        .inactive {
            background: #f8d7da;
            color: #842029;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .metric h3 {
            margin-top: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .metric-value {
            font-size: 25px;
            font-weight: bold;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
        }

        .blue {
            background: #0d6efd;
        }

        .green {
            background: #198754;
        }

        .red {
            background: #dc3545;
        }

        .orange {
            background: #fd7e14;
        }

        .purple {
            background: #6f42c1;
        }

        .gray {
            background: #6c757d;
        }

        .key {
            padding: 10px;
            background: #212529;
            color: white;
            border-radius: 6px;
            margin-bottom: 7px;
            font-family: monospace;
            overflow-x: auto;
        }

        .warning {
            background: #fff3cd;
            color: #664d03;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        @media(max-width:800px) {

            .grid {
                grid-template-columns: 1fr;
            }

        }
    </style>

</head>

<body>

    <div class="navbar">

        <a href="/products">
            Products
        </a>

        <a href="/products/search">
            Search
        </a>

        <a href="/cache-performance">
            Performance
        </a>

        <a href="/cache-management">
            Management
        </a>

        <a href="/cache-statistics">
            Statistics
        </a>

    </div>


    <div class="container">

        @if(session('success'))

        <div class="success">
            ✓ {{ session('success') }}
        </div>

        @endif


        <div class="header">

            <h1>
                🧹 Cache Management Dashboard
            </h1>

            <p>
                Monitor, warm, refresh and clear
                Laravel product caches.
            </p>

        </div>


        <!-- Main Cache -->

        <div class="card">

            <h2>
                Active Product Cache
            </h2>


            @if($cacheExists)

            <span class="status active">
                ✓ CACHE EXISTS
            </span>

            @else

            <span class="status inactive">
                ✕ CACHE NOT FOUND
            </span>

            @endif


            <div class="grid">

                <div class="metric">

                    <h3>
                        Cached Products
                    </h3>

                    <div class="metric-value">
                        {{ $cachedProductCount }}
                    </div>

                </div>


                <div class="metric">

                    <h3>
                        TTL
                    </h3>

                    <div class="metric-value">
                        300 seconds
                    </div>

                </div>


                <div class="metric">

                    <h3>
                        Remaining
                    </h3>

                    <div class="metric-value">

                        @if($cacheExists)

                        {{ $remainingSeconds }} sec

                        @else

                        0 sec

                        @endif

                    </div>

                </div>

            </div>


            <div>

                <strong>
                    Last Warmed:
                </strong>

                {{ $warmedAt ?? 'Not warmed yet' }}

            </div>


            <div style="margin-top:10px;">

                <strong>
                    Expires:
                </strong>

                @if($expiresAt)

                {{ date('Y-m-d H:i:s', $expiresAt) }}

                @else

                Not available

                @endif

            </div>


            <div class="buttons">

                <a
                    href="{{ route('cache.warm') }}"
                    class="btn green">
                    🔥 Warm Cache
                </a>


                <a
                    href="{{ route('cache.refresh') }}"
                    class="btn blue">
                    🔄 Refresh Cache
                </a>


                <a
                    href="{{ route('cache.clear') }}"
                    class="btn red"
                    onclick="return confirm('Clear all registered product caches?')">
                    🗑 Clear Cache
                </a>


                <a
                    href="{{ route('cache.clear-all') }}"
                    class="btn purple"
                    onclick="return confirm('Clear ALL registered product and search caches?')">
                    🧹 Clear All Caches
                </a>


                <a
                    href="{{ route('products.index') }}"
                    class="btn gray">
                    📦 Products
                </a>

            </div>

        </div>


        <!-- Dynamic Cache TTL Studio -->

        <div class="card">

            <h2>
                🎛 Dynamic Model Cache TTL & Strategy Control Studio
            </h2>

            <p>
                Select dynamic Time-To-Live (TTL) strategy for cached Eloquent model queries.
                Changing TTL flushes registered model cache tags/keys automatically.
            </p>

            <div style="margin-bottom: 20px;">
                <strong>Current Dynamic Cache Strategy:</strong>
                <span class="status active" style="font-size: 14px; margin-left: 8px;">
                    ⚡ {{ $dynamicTtl }} Seconds
                    @if($dynamicTtl == 300) (5 Minutes - Default)
                    @elseif($dynamicTtl == 3600) (1 Hour)
                    @elseif($dynamicTtl == 86400) (1 Day)
                    @elseif($dynamicTtl >= 31536000) (Permanent / 1 Year)
                    @else (Custom Strategy)
                    @endif
                </span>
            </div>

            <form action="{{ route('cache.update-ttl') }}" method="POST">
                @csrf
                <div class="buttons">
                    <button type="submit" name="ttl" value="300" class="btn blue" style="border:none; cursor:pointer;">
                        ⏱️ 5 Minutes (300s)
                    </button>
                    <button type="submit" name="ttl" value="3600" class="btn green" style="border:none; cursor:pointer;">
                        ⏰ 1 Hour (3600s)
                    </button>
                    <button type="submit" name="ttl" value="86400" class="btn orange" style="border:none; cursor:pointer;">
                        📅 1 Day (86400s)
                    </button>
                    <button type="submit" name="ttl" value="31536000" class="btn purple" style="border:none; cursor:pointer;">
                        ♾️ Permanent (1 Year)
                    </button>
                </div>
            </form>

        </div>


        <!-- Cache Keys -->

        <div class="keys">

            <h2>
                🔑 Cache Key Inspector
            </h2>

            <p>
                These are cache keys registered by the
                product listing and search functionality.
            </p>


            @if(count($cacheKeys))

            @foreach($cacheKeys as $key)

            <div class="key">
                {{ $key }}
            </div>

            @endforeach

            @else

            <div class="warning">
                No registered cache keys found.
            </div>

            @endif

        </div>


        <!-- Concepts -->

        <div class="card">

            <h2>
                💡 Cache Features
            </h2>

            <ul>

                <li>
                    <strong>Warm Cache:</strong>
                    Creates the active-product cache.
                </li>

                <li>
                    <strong>Refresh Cache:</strong>
                    Removes old cached data and
                    loads the latest database data.
                </li>

                <li>
                    <strong>Clear Cache:</strong>
                    Removes registered product caches.
                </li>

                <li>
                    <strong>Clear All Caches:</strong>
                    Removes product listing and
                    search cache keys tracked by this application.
                </li>

                <li>
                    <strong>Cache Key Inspector:</strong>
                    Displays registered dynamic cache keys.
                </li>

                <li>
                    <strong>TTL:</strong>
                    Product listing caches use
                    a 300-second lifetime.
                </li>

            </ul>

        </div>

    </div>

</body>

</html>