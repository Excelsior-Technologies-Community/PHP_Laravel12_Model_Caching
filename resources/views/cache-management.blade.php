<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cache Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .navbar {
            background: #212529;
            padding: 15px 30px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-size: 14px;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header,
        .status-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
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

        .buttons {
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 10px;
        }

        .clear {
            background: #dc3545;
        }

        .warm {
            background: #198754;
        }

        .view {
            background: #0d6efd;
        }

        .info {
            margin-top: 25px;
            color: #6c757d;
        }
    </style>
</head>

<body>

<div class="navbar">
    <a href="/products">Products</a>
    <a href="/products/search">Search & Filter</a>
    <a href="/cache-performance">Performance</a>
    <a href="/cache-management">Cache Management</a>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <div class="header">

        <h1>🧹 Cache Management Dashboard</h1>

        <p>
            Manage the cached active-product dataset.
        </p>

    </div>

    <div class="status-card">

        <h2>Active Product Cache</h2>

        @if($cacheExists)

            <span class="status active">
                ✓ CACHE EXISTS
            </span>

            <p>
                Cached Products:
                <strong>{{ $cachedProductCount }}</strong>
            </p>

        @else

            <span class="status inactive">
                ✕ CACHE NOT FOUND
            </span>

            <p>
                The active product cache has not been created yet.
            </p>

        @endif

        <div class="buttons">

            <a
                href="/cache-management/warm"
                class="btn warm"
            >
                🔥 Rebuild / Warm Cache
            </a>

            <a
                href="/cache-management/clear"
                class="btn clear"
                onclick="return confirm('Clear the active product cache?')"
            >
                🗑 Clear Cache
            </a>

            <a
                href="/products"
                class="btn view"
            >
                View Products
            </a>

        </div>

    </div>

    <div class="info">

        <h3>Cache Management Concepts</h3>

        <ul>
            <li>
                <strong>Cache Exists:</strong>
                Confirms whether the active product cache is available.
            </li>

            <li>
                <strong>Clear Cache:</strong>
                Removes the manually cached active product list.
            </li>

            <li>
                <strong>Warm Cache:</strong>
                Queries the database once and stores the result in cache.
            </li>

            <li>
                After clearing the cache, opening
                <strong>/products</strong>
                creates the cache again.
            </li>
        </ul>

    </div>

</div>

</body>
</html>