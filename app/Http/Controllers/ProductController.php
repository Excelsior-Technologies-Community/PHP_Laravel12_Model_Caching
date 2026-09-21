<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cache Statistics Helper
    |--------------------------------------------------------------------------
    */

    private function incrementCacheStat(string $key): void
    {
        if (!Cache::has($key)) {
            Cache::forever($key, 0);
        }

        Cache::increment($key);
    }

    /*
    |--------------------------------------------------------------------------
    | Seed Products
    |--------------------------------------------------------------------------
    */

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

        Product::create([
            'name' => 'OnePlus 12',
            'price' => 64999,
            'status' => 1
        ]);

        Product::create([
            'name' => 'Google Pixel 9',
            'price' => 79999,
            'status' => 1
        ]);

        Product::create([
            'name' => 'Xiaomi 14',
            'price' => 69999,
            'status' => 1
        ]);

        Product::create([
            'name' => 'Nothing Phone 2',
            'price' => 39999,
            'status' => 1
        ]);

        Cache::forget('active_products');

        return redirect('/products')
            ->with('success', 'Products seeded successfully and cache cleared.');
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Product Listing
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $cacheKey = 'active_products';

        if (Cache::has($cacheKey)) {
            $this->incrementCacheStat('model_cache_total_requests');
            $this->incrementCacheStat('model_cache_hits');
            $this->incrementCacheStat('model_cache_product_hits');
        } else {
            $this->incrementCacheStat('model_cache_total_requests');
            $this->incrementCacheStat('model_cache_misses');
            $this->incrementCacheStat('model_cache_product_misses');
        }

        $products = Cache::remember($cacheKey, 60, function () {
            logger("Database Query Executed 🔥");

            return Product::where('status', 1)->get();
        });

        return view('products', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | Products Without Cache
    |--------------------------------------------------------------------------
    */

    public function withoutCache()
    {
        $this->incrementCacheStat('model_cache_database_requests');

        logger("Database Query Executed (No Cache) ❌");

        $products = Product::disableCache()
            ->where('status', 1)
            ->get();

        return response()->json(
            $products,
            200,
            [],
            JSON_PRETTY_PRINT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Product
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        Cache::forget('active_products');

        return redirect('/products')
            ->with('success', 'Product deleted successfully and cache cleared.');
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Performance Dashboard
    |--------------------------------------------------------------------------
    */

    public function performance()
    {
        /*
        |--------------------------------------------------------------------------
        | Cache Miss
        |--------------------------------------------------------------------------
        */

        Cache::forget('performance_test_cache');

        $start = microtime(true);

        Cache::remember('performance_test_cache', 60, function () {
            return Product::disableCache()
                ->where('status', 1)
                ->get();
        });

        $cacheMissTime = (microtime(true) - $start) * 1000;

        /*
        |--------------------------------------------------------------------------
        | Cache Hit
        |--------------------------------------------------------------------------
        */

        $start = microtime(true);

        Cache::remember('performance_test_cache', 60, function () {
            return Product::disableCache()
                ->where('status', 1)
                ->get();
        });

        $cacheHitTime = (microtime(true) - $start) * 1000;

        /*
        |--------------------------------------------------------------------------
        | Direct Database
        |--------------------------------------------------------------------------
        */

        $start = microtime(true);

        Product::disableCache()
            ->where('status', 1)
            ->get();

        $databaseTime = (microtime(true) - $start) * 1000;

        $improvement = 0;

        if ($databaseTime > 0) {
            $improvement = (($databaseTime - $cacheHitTime) / $databaseTime) * 100;
        }

        return view('cache-performance', [
            'cacheMissTime' => round($cacheMissTime, 2),
            'cacheHitTime' => round($cacheHitTime, 2),
            'databaseTime' => round($databaseTime, 2),
            'improvement' => round($improvement, 2),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Management Dashboard
    |--------------------------------------------------------------------------
    */

    public function cacheManagement()
    {
        $cacheExists = Cache::has('active_products');

        $cachedProducts = Cache::get('active_products');

        $cachedProductCount = 0;

        if ($cachedProducts instanceof \Illuminate\Support\Collection) {
            $cachedProductCount = $cachedProducts->count();
        }

        return view('cache-management', [
            'cacheExists' => $cacheExists,
            'cachedProductCount' => $cachedProductCount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Cache
    |--------------------------------------------------------------------------
    */

    public function clearCache()
    {
        Cache::forget('active_products');

        return redirect('/cache-management')
            ->with('success', 'Active product cache cleared successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Warm Cache
    |--------------------------------------------------------------------------
    */

    public function warmCache()
    {
        Cache::forget('active_products');

        Cache::remember('active_products', 60, function () {
            return Product::where('status', 1)->get();
        });

        return redirect('/cache-management')
            ->with('success', 'Product cache warmed successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Product Search & Filtering
    |--------------------------------------------------------------------------
    */

public function search(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $minPrice = $request->input('min_price');
    $maxPrice = $request->input('max_price');

    /*
    |--------------------------------------------------------------------------
    | Treat "all" as no status filter
    |--------------------------------------------------------------------------
    */

    if ($status === 'all') {
        $status = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Create unique cache key for this search/filter combination
    |--------------------------------------------------------------------------
    */

    $filters = [
        'search' => $search,
        'status' => $status,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
    ];

    $cacheKey = 'products_search_' . md5(
        json_encode($filters)
    );

    /*
    |--------------------------------------------------------------------------
    | Check Cache HIT / MISS
    |--------------------------------------------------------------------------
    */

    if (Cache::has($cacheKey)) {

        $cacheHit = true;

        $this->incrementCacheStat(
            'model_cache_total_requests'
        );

        $this->incrementCacheStat(
            'model_cache_hits'
        );

        $this->incrementCacheStat(
            'model_cache_search_hits'
        );

    } else {

        $cacheHit = false;

        $this->incrementCacheStat(
            'model_cache_total_requests'
        );

        $this->incrementCacheStat(
            'model_cache_misses'
        );

        $this->incrementCacheStat(
            'model_cache_search_misses'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Products From Cache / Database
    |--------------------------------------------------------------------------
    */

    $products = Cache::remember(
        $cacheKey,
        300,
        function () use (
            $search,
            $status,
            $minPrice,
            $maxPrice
        ) {

            $query = Product::disableCache();

            /*
            |--------------------------------------------------------------------------
            | Product Name Search
            |--------------------------------------------------------------------------
            */

            if ($search !== null && $search !== '') {

                $query->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Status Filter
            |--------------------------------------------------------------------------
            */

            if ($status !== null && $status !== '') {

                $query->where(
                    'status',
                    $status
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Minimum Price Filter
            |--------------------------------------------------------------------------
            */

            if ($minPrice !== null && $minPrice !== '') {

                $query->where(
                    'price',
                    '>=',
                    $minPrice
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Maximum Price Filter
            |--------------------------------------------------------------------------
            */

            if ($maxPrice !== null && $maxPrice !== '') {

                $query->where(
                    'price',
                    '<=',
                    $maxPrice
                );
            }

            return $query
                ->orderBy('name')
                ->get();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Return Search View
    |--------------------------------------------------------------------------
    */

    return view('product-search', [
        'products' => $products,
        'search' => $search,
        'status' => $status,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'cacheHit' => $cacheHit,
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | Cache Statistics & Hit/Miss Analytics Dashboard
    |--------------------------------------------------------------------------
    */

    public function cacheStatistics()
    {
        $totalRequests = (int) Cache::get(
            'model_cache_total_requests',
            0
        );

        $hits = (int) Cache::get(
            'model_cache_hits',
            0
        );

        $misses = (int) Cache::get(
            'model_cache_misses',
            0
        );

        $productHits = (int) Cache::get(
            'model_cache_product_hits',
            0
        );

        $productMisses = (int) Cache::get(
            'model_cache_product_misses',
            0
        );

        $searchHits = (int) Cache::get(
            'model_cache_search_hits',
            0
        );

        $searchMisses = (int) Cache::get(
            'model_cache_search_misses',
            0
        );

        $databaseRequests = (int) Cache::get(
            'model_cache_database_requests',
            0
        );

        $hitRate = 0;
        $missRate = 0;

        if ($totalRequests > 0) {
            $hitRate = ($hits / $totalRequests) * 100;
            $missRate = ($misses / $totalRequests) * 100;
        }

        $totalProductRequests = $productHits + $productMisses;

        $productHitRate = 0;

        if ($totalProductRequests > 0) {
            $productHitRate = (
                $productHits /
                $totalProductRequests
            ) * 100;
        }

        $totalSearchRequests = $searchHits + $searchMisses;

        $searchHitRate = 0;

        if ($totalSearchRequests > 0) {
            $searchHitRate = (
                $searchHits /
                $totalSearchRequests
            ) * 100;
        }

        return view('cache-statistics', [
            'totalRequests' => $totalRequests,
            'hits' => $hits,
            'misses' => $misses,
            'hitRate' => round($hitRate, 2),
            'missRate' => round($missRate, 2),
            'productHits' => $productHits,
            'productMisses' => $productMisses,
            'productHitRate' => round($productHitRate, 2),
            'searchHits' => $searchHits,
            'searchMisses' => $searchMisses,
            'searchHitRate' => round($searchHitRate, 2),
            'databaseRequests' => $databaseRequests,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Cache Statistics
    |--------------------------------------------------------------------------
    */

    public function resetCacheStatistics()
    {
        $statisticsKeys = [
            'model_cache_total_requests',
            'model_cache_hits',
            'model_cache_misses',
            'model_cache_product_hits',
            'model_cache_product_misses',
            'model_cache_search_hits',
            'model_cache_search_misses',
            'model_cache_database_requests',
        ];

        foreach ($statisticsKeys as $key) {
            Cache::forget($key);
        }

        return redirect('/cache-statistics')
            ->with(
                'success',
                'Cache statistics have been reset successfully.'
            );
    }
}