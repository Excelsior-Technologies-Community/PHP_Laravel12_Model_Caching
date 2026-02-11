<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    // Seed the database with sample products and clear cache
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

    // Display cached active products
    public function index()
    {
        $products = Cache::remember('active_products', 60, function () {
            logger("Database Query Executed 🔥");
            return Product::where('status', 1)->get();
        });

        return view('products', compact('products'));
    }

    // Fetch products directly from database without caching
    public function withoutCache()
    {
        logger("Database Query Executed (No Cache) ❌");

        $products = Product::where('status', 1)->get();

        return response()->json($products, 200, [], JSON_PRETTY_PRINT);
    }

    // Delete a product and clear related cache
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        Cache::forget('active_products');

        return "Product Deleted";
    }
}
