<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .price {
            color: green;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 20px;
            background: #e6f7ff;
            color: #007bff;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Product List (Cached)</h1>

    <div class="grid">
        @foreach($products as $product)
            <div class="card">
                <h3>{{ $product->name }}</h3>
                <div class="price">₹ {{ number_format($product->price, 2) }}</div>
                <div class="badge">
                    {{ $product->status ? 'Available' : 'Out of Stock' }}
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
