<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Cache Performance Dashboard</title>

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
        color: white;

        display: flex;
        justify-content: space-between;
        align-items: center;

        flex-wrap: wrap;
        gap: 15px;
    }

    .navbar-brand {
        color: white;
        text-decoration: none;
        font-size: 18px;
        font-weight: bold;
    }

    .nav-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
    }

    .nav-links a:hover {
        background: #343a40;
    }

    .container {
        max-width: 1150px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .header {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;

        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    }

    .header h1 {
        margin: 0 0 10px;
    }

    .header p {
        color: #6c757d;
        margin: 0;
        line-height: 1.6;
    }

    .success {
        background: #d1e7dd;
        color: #0f5132;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 12px;

        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    }

    .card h3 {
        margin: 0 0 12px;
        color: #6c757d;
        font-size: 15px;
    }

    .value {
        font-size: 30px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .description {
        color: #6c757d;
        font-size: 13px;
    }

    .miss {
        color: #dc3545;
    }

    .hit {
        color: #198754;
    }

    .database {
        color: #0d6efd;
    }

    .improvement {
        color: #6f42c1;
    }

    .section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;

        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    }

    .section h2 {
        margin-top: 0;
        margin-bottom: 20px;
    }

    .comparison {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .comparison-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
    }

    .comparison-card h3 {
        margin-top: 0;
    }

    .time {
        font-size: 28px;
        font-weight: bold;
        margin: 10px 0;
    }

    .bar-container {
        margin-top: 15px;
    }

    .bar-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 7px;
        font-size: 14px;
    }

    .bar {
        height: 18px;
        background: #e9ecef;
        border-radius: 20px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 20px;
    }

    .bar-miss {
        background: #dc3545;
    }

    .bar-hit {
        background: #198754;
    }

    .bar-database {
        background: #0d6efd;
    }

    .info-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 18px;
        border-radius: 8px;
        line-height: 1.7;
    }

    .formula {
        margin-top: 15px;
        background: #212529;
        color: white;
        padding: 15px;
        border-radius: 7px;

        font-family: monospace;
        overflow-x: auto;
    }

    .actions {
        margin-top: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .btn {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
    }

    .btn-primary {
        background: #0d6efd;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-success {
        background: #198754;
    }

    @media (max-width: 1000px) {

        .cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .comparison {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 600px) {

        .cards {
            grid-template-columns: 1fr;
        }

        .navbar {
            align-items: flex-start;
        }

    }

</style>


</head>

<body>

<!-- Navigation -->

<div class="navbar">


<a href="/products" class="navbar-brand">
    Laravel Model Caching
</a>

<div class="nav-links">

    <a href="/products">
        Cached Products
    </a>

    <a href="/products/search">
        Search & Filter
    </a>

    <a href="/cache-performance">
        Performance
    </a>

    <a href="/cache-management">
        Cache Management
    </a>

    <a href="/cache-statistics">
        📈 Statistics
    </a>

</div>


</div>

<div class="container">


<!-- Header -->

<div class="header">

    <h1>
        📊 Cache Performance Dashboard
    </h1>

    <p>
        Compare the execution time of cache miss,
        cache hit and direct database requests.
    </p>

    <div class="actions">

        <a href="/products"
           class="btn btn-primary">

            View Products

        </a>

        <a href="/cache-statistics"
           class="btn btn-success">

            📈 View Cache Statistics

        </a>

        <a href="/cache-management"
           class="btn btn-secondary">

            🧹 Cache Management

        </a>

    </div>

</div>


<!-- Performance Cards -->

<div class="cards">


    <!-- Cache Miss -->

    <div class="card">

        <h3>
            🔴 Cache Miss
        </h3>

        <div class="value miss">

            {{ $cacheMissTime }} ms

        </div>

        <div class="description">

            First request loads the data
            and stores it in cache.

        </div>

    </div>


    <!-- Cache Hit -->

    <div class="card">

        <h3>
            🟢 Cache Hit
        </h3>

        <div class="value hit">

            {{ $cacheHitTime }} ms

        </div>

        <div class="description">

            Request is served directly
            from the cache.

        </div>

    </div>


    <!-- Database -->

    <div class="card">

        <h3>
            🔵 Direct Database
        </h3>

        <div class="value database">

            {{ $databaseTime }} ms

        </div>

        <div class="description">

            Request directly queries
            the database.

        </div>

    </div>


    <!-- Improvement -->

    <div class="card">

        <h3>
            ⚡ Cache Improvement
        </h3>

        <div class="value improvement">

            {{ $improvement }}%

        </div>

        <div class="description">

            Estimated improvement compared
            with the direct database request.

        </div>

    </div>

</div>


<!-- Detailed Comparison -->

<div class="section">

    <h2>
        ⏱ Execution Time Comparison
    </h2>


    <div class="comparison">


        <!-- Cache Miss -->

        <div class="comparison-card">

            <h3>
                🔴 Cache Miss
            </h3>

            <div class="time">

                {{ $cacheMissTime }} ms

            </div>

            <p>
                Database query executes and
                the result is stored in cache.
            </p>

        </div>


        <!-- Cache Hit -->

        <div class="comparison-card">

            <h3>
                🟢 Cache Hit
            </h3>

            <div class="time">

                {{ $cacheHitTime }} ms

            </div>

            <p>
                Data is returned from cache
                without the original product query.
            </p>

        </div>


        <!-- Database -->

        <div class="comparison-card">

            <h3>
                🔵 Direct Database
            </h3>

            <div class="time">

                {{ $databaseTime }} ms

            </div>

            <p>
                Product data is retrieved
                directly from the database.
            </p>

        </div>

    </div>

</div>


<!-- Visual Comparison -->

<div class="section">

    <h2>
        📈 Performance Visualization
    </h2>


    @php

        $maxTime = max(
            $cacheMissTime,
            $cacheHitTime,
            $databaseTime,
            0.01
        );

        $missWidth =
            ($cacheMissTime / $maxTime) * 100;

        $hitWidth =
            ($cacheHitTime / $maxTime) * 100;

        $databaseWidth =
            ($databaseTime / $maxTime) * 100;

    @endphp


    <!-- Cache Miss -->

    <div class="bar-container">

        <div class="bar-label">

            <span>
                Cache Miss
            </span>

            <strong>
                {{ $cacheMissTime }} ms
            </strong>

        </div>

        <div class="bar">

            <div
                class="bar-fill bar-miss"
                style="width: {{ min($missWidth, 100) }}%;"
            ></div>

        </div>

    </div>


    <!-- Cache Hit -->

    <div class="bar-container">

        <div class="bar-label">

            <span>
                Cache Hit
            </span>

            <strong>
                {{ $cacheHitTime }} ms
            </strong>

        </div>

        <div class="bar">

            <div
                class="bar-fill bar-hit"
                style="width: {{ min($hitWidth, 100) }}%;"
            ></div>

        </div>

    </div>


    <!-- Database -->

    <div class="bar-container">

        <div class="bar-label">

            <span>
                Direct Database
            </span>

            <strong>
                {{ $databaseTime }} ms
            </strong>

        </div>

        <div class="bar">

            <div
                class="bar-fill bar-database"
                style="width: {{ min($databaseWidth, 100) }}%;"
            ></div>

        </div>

    </div>

</div>


<!-- Explanation -->

<div class="section">

    <h2>
        💡 How This Test Works
    </h2>

    <div class="info-box">

        <strong>1. Cache Miss</strong>

        <br>

        The existing test cache is cleared first.
        Laravel then retrieves the products and stores
        the result in the cache.

        <br><br>


        <strong>2. Cache Hit</strong>

        <br>

        The same cached data is requested again.
        Laravel can return the existing cached value.

        <br><br>


        <strong>3. Direct Database</strong>

        <br>

        The product query is executed directly against
        the database without using the model cache.

        <br><br>


        <strong>4. Improvement</strong>

        <div class="formula">

            Improvement =
            ((Database Time - Cache Hit Time)
            / Database Time) × 100

        </div>

    </div>

</div>


<!-- Result Summary -->

<div class="section">

    <h2>
        📋 Performance Summary
    </h2>

    <div class="info-box">

        <strong>
            Cache Miss:
        </strong>

        {{ $cacheMissTime }} ms

        <br>

        <strong>
            Cache Hit:
        </strong>

        {{ $cacheHitTime }} ms

        <br>

        <strong>
            Direct Database:
        </strong>

        {{ $databaseTime }} ms

        <br>

        <strong>
            Measured Cache Improvement:
        </strong>

        {{ $improvement }}%

    </div>

</div>


</div>

</body>

</html>
