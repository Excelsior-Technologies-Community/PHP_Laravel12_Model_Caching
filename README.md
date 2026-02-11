#  PHP_Laravel12_Model_Caching

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![Cache](https://img.shields.io/badge/Cache-File%20Driver-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

---

##  Overview

This project demonstrates **Model Caching in Laravel 12** using:

* File-based cache driver
* Manual cache handling with `Cache::remember()`
* Cache invalidation on delete
* Performance comparison between cached and non-cached queries

---

##  Features

* Laravel 12 Fresh Installation
* Product Model & Migration
* File Cache Configuration
* Cached Product Listing
* Non-Cached Product Comparison Endpoint
* Cache Clearing on Delete
* Logging to Verify DB Query Execution

---

##  Folder Structure

```
Laravel12_Model_Caching/
│
├── app/
│   ├── Http/Controllers/ProductController.php
│   └── Models/Product.php
│
├── database/
│   └── migrations/xxxx_create_products_table.php
│
├── resources/
│   └── views/products.blade.php
│
├── routes/
│   └── web.php
│
├── .env
└── README.md
```

---

#  Implementation Steps

## Step 1: Install Laravel 12 Project

Create New Project:

```bash
composer create-project laravel/laravel Laravel12_Model_Caching
```

Start development server:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## Step 2: Configure Database

Open `.env` file and update database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=demo
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 3: Create Product Model & Migration

Generate model with migration:

```bash
php artisan make:model Product -m
```

Open migration file inside:

```
database/migrations/xxxx_create_products_table.php
```

Replace `up()` method with:

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->boolean('status')->default(1);
        $table->timestamps();
    });
}
```

Run migration:

```bash
php artisan migrate
```

---

## Step 4: Configure Cache (File Driver)

Open `.env` and ensure:

```env
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

Clear config cache:

```bash
php artisan optimize:clear
```

---

## Step 5: Update Product Model

Open:

```
app/Models/Product.php
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Product extends Model
{
    use Cachable;

    protected $fillable = [
        'name',
        'price',
        'status'
    ];
}
```

---

## Step 6: Create Product Controller

Generate controller:

```bash
php artisan make:controller ProductController
```

Open:

```
app/Http/Controllers/ProductController.php
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function seed()
    {
        Product::truncate();

        Product::create([
            'name' => 'iPhone 15',
            'price' => 79999,
            'status' => 1
        ]);

        Product::create([
            'name' => 'Samsung S24',
            'price' => 74999,
            'status' => 1
        ]);

        Cache::forget('active_products');

        return "Products Created Successfully";
    }

    public function index()
    {
        $products = Cache::remember('active_products', 60, function () {
            logger("Database Query Executed 🔥");
            return Product::where('status', 1)->get();
        });

        return view('products', compact('products'));
    }

    public function withoutCache()
    {
        logger("Database Query Executed (No Cache) ❌");

        $products = Product::where('status', 1)->get();

        return response()->json($products, 200, [], JSON_PRETTY_PRINT);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        Cache::forget('active_products');

        return "Product Deleted";
    }
}
```

---

## Step 7: Define Routes

Open:

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/seed', [ProductController::class, 'seed']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products-no-cache', [ProductController::class, 'withoutCache']);
Route::get('/delete/{id}', [ProductController::class, 'delete']);
```

---

## Step 8: Create Blade View

Create file:

resources/views/products.blade.php

```
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

```

## Step 9: Testing the Application

Insert Products:

```
http://127.0.0.1:8000/seed
```
<img width="364" height="103" alt="Screenshot 2026-02-11 170532" src="https://github.com/user-attachments/assets/729961ae-f940-4f4b-89dc-17f52bbc3aaa" />


View Products:

```
http://127.0.0.1:8000/products
```
<img width="1666" height="406" alt="Screenshot 2026-02-11 170541" src="https://github.com/user-attachments/assets/36030925-2530-4a73-9147-bf9210feeb6f" />


Refresh multiple times.

Check logs:

```
storage/logs/laravel.log
```

You should see:

```
Database Query Executed 🔥
```
<img width="602" height="139" alt="Screenshot 2026-02-11 170627" src="https://github.com/user-attachments/assets/e26b1229-8d2e-4386-b8f6-87a865800b8e" />

---

## Step 10: Test Delete Function

Delete product:

```
http://127.0.0.1:8000/delete/1
```

Refresh `/products`.

Deleted product will no longer appear.
Cache clears automatically.

---


