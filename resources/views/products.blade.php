<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Cached Products</title>

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
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .navbar-brand {
            font-size: 18px;
            font-weight: bold;
            color: white;
            text-decoration: none;
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
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .header h1 {
            margin: 0 0 10px;
        }

        .header p {
            color: #6c757d;
            margin: 0;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box {
            background: #e7f1ff;
            color: #084298;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .price {
            color: #198754;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 20px;
            background: #e7f1ff;
            color: #0d6efd;
        }

        .empty {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
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
            background: #0d6efd;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-success {
            background: #198754;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-danger {
            background: #dc3545;
        }

        .feature-section {
            margin-bottom: 25px;
        }

        .feature-title {
            margin-bottom: 15px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .feature-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 17px;
        }

        .feature-card p {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
            min-height: 42px;
        }

        @media (max-width: 800px) {
            .feature-grid {
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
                📈 Cache Statistics
            </a>

        </div>


    </div>

    <div class="container">


        @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

        @endif


        <div class="header">

            <h1>
                Product List
            </h1>

            <p>
                Products are retrieved using Laravel cache.
            </p>


            <div class="actions">

                <a href="/seed" class="btn">
                    🌱 Seed Products
                </a>

                <a href="/products/search" class="btn btn-secondary">
                    🔎 Search Products
                </a>

                <a href="/cache-performance" class="btn btn-success">
                    📊 Performance
                </a>

                <a href="/cache-management" class="btn btn-warning">
                    🧹 Cache Management
                </a>

                <a href="/cache-statistics" class="btn btn-danger">
                    📈 Cache Statistics
                </a>

            </div>


            <div class="info-box">

                <strong>Model Caching:</strong>

                This product list uses Laravel caching to reduce
                repeated database queries.

                Visit
                <strong>Cache Statistics</strong>
                to monitor cache hits, misses and hit rate.

            </div>

        </div>


        <!-- Model Caching Features -->

        <div class="feature-section">

            <h2 class="feature-title">
                Laravel Model Caching Features
            </h2>


            <div class="feature-grid">

                <div class="feature-card">

                    <h3>
                        📊 Cache Performance
                    </h3>

                    <p>
                        Compare cached requests with direct
                        database requests and measure execution time.
                    </p>

                    <a href="/cache-performance"
                        class="btn btn-success">

                        View Performance

                    </a>

                </div>


                <div class="feature-card">

                    <h3>
                        🧹 Cache Management
                    </h3>

                    <p>
                        Check cache status, clear product cache
                        and warm the cache for faster requests.
                    </p>

                    <a href="/cache-management"
                        class="btn btn-warning">

                        Manage Cache

                    </a>

                </div>


                <div class="feature-card">

                    <h3>
                        📈 Cache Analytics
                    </h3>

                    <p>
                        Monitor cache hits, misses, hit rate,
                        search cache and direct database requests.
                    </p>

                    <a href="/cache-statistics"
                        class="btn btn-danger">

                        View Analytics

                    </a>

                </div>

            </div>

        </div>


        @if($products->count())

        <div class="grid">

            @foreach($products as $product)

            <div class="card">

                <h3>
                    {{ $product->name }}
                </h3>


                <div class="price">

                    ₹ {{ number_format($product->price, 2) }}

                </div>


                <div class="badge">

                    {{ $product->status ? 'Available' : 'Out of Stock' }}

                </div>


                <div style="margin-top: 20px;">

                    <a
                        href="/delete/{{ $product->id }}"
                        class="btn btn-danger"
                        onclick="return confirm('Delete this product?')">
                        Delete
                    </a>

                </div>

            </div>

            @endforeach

        </div>

        @else

        <div class="empty">

            <h2>
                No Products Found
            </h2>

            <p>
                Use the Seed Products button to create sample products.
            </p>

            <div style="margin-top: 20px;">

                <a href="/seed" class="btn">
                    🌱 Seed Products
                </a>

            </div>

        </div>

        @endif


    </div>

</body>

</html>