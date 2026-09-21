<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cached Product Search</title>

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

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 25px;
        }

        .box h1 {
            margin-top: 0;
        }

        .box p {
            color: #6c757d;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 11px 18px;
            border-radius: 6px;
            color: white;
            background: #0d6efd;
            cursor: pointer;
            text-decoration: none;
            margin-right: 8px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .reset {
            background: #6c757d;
        }

        .cache-status {
            margin-top: 20px;
            padding: 12px 15px;
            border-radius: 7px;
        }

        .hit {
            background: #d1e7dd;
            color: #0f5132;
        }

        .miss {
            background: #fff3cd;
            color: #664d03;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .product {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .product h3 {
            margin-top: 0;
            margin-bottom: 12px;
        }

        .price {
            color: #198754;
            font-weight: bold;
            font-size: 18px;
        }

        .badge {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            background: #e7f1ff;
            color: #0d6efd;
            font-size: 12px;
        }

        .empty {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
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

    <div class="box">

        <h1>🔎 Cached Product Search & Filtering</h1>

        <p>
            Search and filter results are stored using unique cache keys.
        </p>

        <form method="GET" action="{{ url('/products/search') }}">

            <div class="form-grid">

                <div>
                    <label for="search">
                        Product Name
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="e.g. iPhone"
                    >
                </div>

                <div>
                    <label for="status">
                        Status
                    </label>

                    <select id="status" name="status">

                        <option
                            value="all"
                            {{ $status === 'all' ? 'selected' : '' }}
                        >
                            All
                        </option>

                        <option
                            value="1"
                            {{ $status === '1' ? 'selected' : '' }}
                        >
                            Available
                        </option>

                        <option
                            value="0"
                            {{ $status === '0' ? 'selected' : '' }}
                        >
                            Out of Stock
                        </option>

                    </select>
                </div>

                <div>
                    <label for="min_price">
                        Minimum Price
                    </label>

                    <input
                        type="number"
                        id="min_price"
                        name="min_price"
                        value="{{ $minPrice }}"
                        placeholder="e.g. 50000"
                    >
                </div>

                <div>
                    <label for="max_price">
                        Maximum Price
                    </label>

                    <input
                        type="number"
                        id="max_price"
                        name="max_price"
                        value="{{ $maxPrice }}"
                        placeholder="e.g. 80000"
                    >
                </div>

            </div>

            <div class="buttons">

                <button type="submit" class="btn">
                    🔍 Search & Filter
                </button>

                <a
                    href="{{ url('/products/search') }}"
                    class="btn reset"
                >
                    Reset
                </a>

            </div>

        </form>

        @if($cacheHit)

            <div class="cache-status hit">
                ⚡
                <strong>Cache HIT:</strong>
                This exact search/filter combination was already cached.
            </div>

        @else

            <div class="cache-status miss">
                🔥
                <strong>Cache MISS:</strong>
                Database was queried and this result has now been cached.
            </div>

        @endif

    </div>


    @if($products->count())

        <div class="grid">

            @foreach($products as $product)

                <div class="product">

                    <h3>
                        {{ $product->name }}
                    </h3>

                    <div class="price">
                        ₹ {{ number_format($product->price, 2) }}
                    </div>

                    <span class="badge">
                        {{ $product->status ? 'Available' : 'Out of Stock' }}
                    </span>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty">

            <h2>No Products Found</h2>

            <p>
                Try another search or filter combination.
            </p>

        </div>

    @endif

</div>

</body>
</html>