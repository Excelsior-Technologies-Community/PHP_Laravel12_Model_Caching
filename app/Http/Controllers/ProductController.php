<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cache Constants
    |--------------------------------------------------------------------------
    */

    private const ACTIVE_PRODUCTS_CACHE = 'active_products';

    private const CACHE_TTL = 300;

    private const CACHE_KEYS_REGISTRY = 'model_cache_keys';

    private const CACHE_WARMED_AT = 'model_cache_warmed_at';

    private const CACHE_EXPIRES_AT = 'model_cache_expires_at';

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
    | Register Cache Key
    |--------------------------------------------------------------------------
    */

    private function registerCacheKey(string $cacheKey): void
    {
        $keys = Cache::get(
            self::CACHE_KEYS_REGISTRY,
            []
        );

        if (!in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;

            Cache::forever(
                self::CACHE_KEYS_REGISTRY,
                $keys
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Forget All Registered Product Caches
    |--------------------------------------------------------------------------
    */

    private function clearRegisteredCaches(): void
    {
        $keys = Cache::get(
            self::CACHE_KEYS_REGISTRY,
            []
        );

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget(
            self::ACTIVE_PRODUCTS_CACHE
        );

        Cache::forget(
            self::CACHE_WARMED_AT
        );

        Cache::forget(
            self::CACHE_EXPIRES_AT
        );

        Cache::forget(
            self::CACHE_KEYS_REGISTRY
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Seed Products
    |--------------------------------------------------------------------------
    */

    public function seed()
    {
        Product::truncate();

        $products = [
            [
                'name' => 'iPhone 15',
                'price' => 79999,
                'status' => 1,
            ],
            [
                'name' => 'Samsung S24',
                'price' => 74999,
                'status' => 1,
            ],
            [
                'name' => 'OnePlus 12',
                'price' => 64999,
                'status' => 1,
            ],
            [
                'name' => 'Google Pixel 9',
                'price' => 79999,
                'status' => 1,
            ],
            [
                'name' => 'Xiaomi 14',
                'price' => 69999,
                'status' => 1,
            ],
            [
                'name' => 'Nothing Phone 2',
                'price' => 39999,
                'status' => 1,
            ],
            [
                'name' => 'Realme GT 7',
                'price' => 39999,
                'status' => 0,
            ],
            [
                'name' => 'Vivo X100',
                'price' => 59999,
                'status' => 1,
            ],
            [
                'name' => 'Oppo Find X8',
                'price' => 69999,
                'status' => 1,
            ],
            [
                'name' => 'Motorola Edge 50',
                'price' => 44999,
                'status' => 0,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        /*
        |--------------------------------------------------------------------------
        | Clear All Existing Caches
        |--------------------------------------------------------------------------
        */

        $this->clearRegisteredCaches();

        /*
        |--------------------------------------------------------------------------
        | Reset Statistics
        |--------------------------------------------------------------------------
        */

        $this->resetStatisticsWithoutRedirect();

        return redirect('/products')
            ->with(
                'success',
                '10 products seeded successfully and all product caches cleared.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Product Listing
    |
    | Features:
    | 1. Search
    | 2. Status filter
    | 3. Price filter
    | 4. Sorting
    | 5. Numeric Pagination
    |
    | DEFAULT SORT = ID ASCENDING
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $status = $request->input('status');

        $minPrice = $request->input('min_price');

        $maxPrice = $request->input('max_price');

        /*
        |--------------------------------------------------------------------------
        | Default Sorting
        |
        | Changed from id_desc to id_asc
        |--------------------------------------------------------------------------
        */

        $sort = $request->input(
            'sort',
            'id_asc'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Sort
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'id_asc',
            'id_desc',
            'name_asc',
            'name_desc',
            'price_asc',
            'price_desc',
            'newest',
            'oldest',
        ];

        /*
        |--------------------------------------------------------------------------
        | Invalid Sort
        |
        | Default is ID ASCENDING
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $sort,
            $allowedSorts,
            true
        )) {
            $sort = 'id_asc';
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Status
        |--------------------------------------------------------------------------
        */

        if (
            $status === 'all' ||
            $status === ''
        ) {
            $status = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Unique Cache Key
        |--------------------------------------------------------------------------
        */

        $filters = [
            'search' => $search,
            'status' => $status,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
        ];

        $cacheKey =
            'product_listing_' .
            md5(
                json_encode($filters)
            );

        $startTime = microtime(true);
        $dynamicTtl = (int) Cache::get('model_cache_dynamic_ttl', self::CACHE_TTL);

        /*
        |--------------------------------------------------------------------------
        | Cache Hit / Miss
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
        } else {
            $cacheHit = false;

            $this->incrementCacheStat(
                'model_cache_total_requests'
            );

            $this->incrementCacheStat(
                'model_cache_misses'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Cached Query
        |--------------------------------------------------------------------------
        */

        $products = Cache::remember(
            $cacheKey,
            $dynamicTtl,
            function () use (
                $search,
                $status,
                $minPrice,
                $maxPrice,
                $sort
            ) {
                logger(
                    'Database Query Executed for Product Listing 🔥'
                );

                $query = Product::disableCache();

                /*
                |--------------------------------------------------------------------------
                | Search
                |--------------------------------------------------------------------------
                */

                if ($search !== '') {
                    $query->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                if ($status !== null) {
                    $query->where(
                        'status',
                        $status
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Minimum Price
                |--------------------------------------------------------------------------
                */

                if (
                    $minPrice !== null &&
                    $minPrice !== ''
                ) {
                    $query->where(
                        'price',
                        '>=',
                        $minPrice
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Maximum Price
                |--------------------------------------------------------------------------
                */

                if (
                    $maxPrice !== null &&
                    $maxPrice !== ''
                ) {
                    $query->where(
                        'price',
                        '<=',
                        $maxPrice
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Sorting
                |--------------------------------------------------------------------------
                */

                switch ($sort) {
                    case 'id_asc':
                        $query->orderBy(
                            'id',
                            'asc'
                        );
                        break;

                    case 'id_desc':
                        $query->orderBy(
                            'id',
                            'desc'
                        );
                        break;

                    case 'name_asc':
                        $query->orderBy(
                            'name',
                            'asc'
                        );
                        break;

                    case 'name_desc':
                        $query->orderBy(
                            'name',
                            'desc'
                        );
                        break;

                    case 'price_asc':
                        $query->orderBy(
                            'price',
                            'asc'
                        );
                        break;

                    case 'price_desc':
                        $query->orderBy(
                            'price',
                            'desc'
                        );
                        break;

                    case 'newest':
                        $query->orderBy(
                            'created_at',
                            'desc'
                        );
                        break;

                    case 'oldest':
                        $query->orderBy(
                            'created_at',
                            'asc'
                        );
                        break;

                    default:
                        /*
                        |--------------------------------------------------------------------------
                        | Safety Default
                        |
                        | Always ID ASC
                        |--------------------------------------------------------------------------
                        */

                        $query->orderBy(
                            'id',
                            'asc'
                        );
                        break;
                }

                return $query->get();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Register Cache Key
        |--------------------------------------------------------------------------
        */

        $this->registerCacheKey(
            $cacheKey
        );

        /*
        |--------------------------------------------------------------------------
        | Numeric Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = 5;

        $currentPage =
            LengthAwarePaginator::resolveCurrentPage();

        $total = $products->count();

        $currentItems = $products
            ->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )
            ->values();

        $paginatedProducts =
            new LengthAwarePaginator(
                $currentItems,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => url('/products'),
                    'query' => $request->query(),
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Product Statistics
        |--------------------------------------------------------------------------
        */

        $totalProducts =
            Product::disableCache()->count();

        $availableProducts =
            Product::disableCache()
                ->where('status', 1)
                ->count();

        $outOfStockProducts =
            Product::disableCache()
                ->where('status', 0)
                ->count();

        $inventoryValue =
            Product::disableCache()
                ->where('status', 1)
                ->sum('price');

        $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'products',
            [
                'products' =>
                    $paginatedProducts,

                'search' =>
                    $search,

                'status' =>
                    $status,

                'minPrice' =>
                    $minPrice,

                'maxPrice' =>
                    $maxPrice,

                'sort' =>
                    $sort,

                'cacheHit' =>
                    $cacheHit,

                'cacheKey' =>
                    $cacheKey,

                'executionTimeMs' =>
                    $executionTimeMs,

                'dynamicTtl' =>
                    $dynamicTtl,

                'totalProducts' =>
                    $totalProducts,

                'availableProducts' =>
                    $availableProducts,

                'outOfStockProducts' =>
                    $outOfStockProducts,

                'inventoryValue' =>
                    $inventoryValue,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Products Without Cache
    |--------------------------------------------------------------------------
    */

    public function withoutCache()
    {
        $this->incrementCacheStat(
            'model_cache_database_requests'
        );

        logger(
            'Database Query Executed (No Cache) ❌'
        );

        $products =
            Product::disableCache()
                ->where('status', 1)
                ->orderBy('id', 'asc')
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

        /*
        |--------------------------------------------------------------------------
        | Clear Every Related Cache
        |--------------------------------------------------------------------------
        */

        $this->clearRegisteredCaches();

        return redirect('/products')
            ->with(
                'success',
                'Product deleted successfully and all product caches were cleared.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(Request $request)
    {
        $ids = $request->input(
            'ids',
            []
        );

        if (
            !is_array($ids) ||
            count($ids) === 0
        ) {
            return redirect('/products')
                ->with(
                    'error',
                    'Please select at least one product.'
                );
        }

        $ids = array_map(
            'intval',
            $ids
        );

        $deleted =
            Product::disableCache()
                ->whereIn('id', $ids)
                ->delete();

        /*
        |--------------------------------------------------------------------------
        | Clear All Related Caches
        |--------------------------------------------------------------------------
        */

        $this->clearRegisteredCaches();

        return redirect('/products')
            ->with(
                'success',
                $deleted .
                ' product(s) deleted successfully and all product caches were cleared.'
            );
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

        Cache::forget(
            'performance_test_cache'
        );

        $start = microtime(true);

        Cache::remember(
            'performance_test_cache',
            60,
            function () {
                return Product::disableCache()
                    ->where('status', 1)
                    ->get();
            }
        );

        $cacheMissTime =
            (microtime(true) - $start) * 1000;

        /*
        |--------------------------------------------------------------------------
        | Cache Hit
        |--------------------------------------------------------------------------
        */

        $start = microtime(true);

        Cache::remember(
            'performance_test_cache',
            60,
            function () {
                return Product::disableCache()
                    ->where('status', 1)
                    ->get();
            }
        );

        $cacheHitTime =
            (microtime(true) - $start) * 1000;

        /*
        |--------------------------------------------------------------------------
        | Direct Database
        |--------------------------------------------------------------------------
        */

        $start = microtime(true);

        Product::disableCache()
            ->where('status', 1)
            ->get();

        $databaseTime =
            (microtime(true) - $start) * 1000;

        $improvement = 0;

        if ($databaseTime > 0) {
            $improvement =
                (
                    ($databaseTime - $cacheHitTime)
                    / $databaseTime
                ) * 100;
        }

        return view(
            'cache-performance',
            [
                'cacheMissTime' =>
                    round(
                        $cacheMissTime,
                        2
                    ),

                'cacheHitTime' =>
                    round(
                        $cacheHitTime,
                        2
                    ),

                'databaseTime' =>
                    round(
                        $databaseTime,
                        2
                    ),

                'improvement' =>
                    round(
                        $improvement,
                        2
                    ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Management Dashboard
    |--------------------------------------------------------------------------
    */

    public function cacheManagement()
    {
        $cacheExists =
            Cache::has(
                self::ACTIVE_PRODUCTS_CACHE
            );

        $cachedProducts =
            Cache::get(
                self::ACTIVE_PRODUCTS_CACHE
            );

        $cachedProductCount = 0;

        if (
            $cachedProducts instanceof
            \Illuminate\Support\Collection
        ) {
            $cachedProductCount =
                $cachedProducts->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Cache Metadata
        |--------------------------------------------------------------------------
        */

        $warmedAt =
            Cache::get(
                self::CACHE_WARMED_AT
            );

        $expiresAt =
            Cache::get(
                self::CACHE_EXPIRES_AT
            );

        $remainingSeconds = 0;

        if ($expiresAt) {
            $remainingSeconds = max(
                0,
                $expiresAt - now()->timestamp
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Registered Cache Keys
        |--------------------------------------------------------------------------
        */

        $cacheKeys =
            Cache::get(
                self::CACHE_KEYS_REGISTRY,
                []
            );

        return view(
            'cache-management',
            [
                'cacheExists' =>
                    $cacheExists,

                'cachedProductCount' =>
                    $cachedProductCount,

                'warmedAt' =>
                    $warmedAt,

                'expiresAt' =>
                    $expiresAt,

                'remainingSeconds' =>
                    $remainingSeconds,

                'cacheKeys' =>
                    $cacheKeys,

                'dynamicTtl' =>
                    (int) Cache::get('model_cache_dynamic_ttl', self::CACHE_TTL),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Main Cache
    |--------------------------------------------------------------------------
    */

    public function clearCache()
    {
        $this->clearRegisteredCaches();

        return redirect('/cache-management')
            ->with(
                'success',
                'All registered product caches cleared successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Warm Cache
    |--------------------------------------------------------------------------
    */

    public function warmCache()
    {
        Cache::forget(
            self::ACTIVE_PRODUCTS_CACHE
        );

        $products =
            Cache::remember(
                self::ACTIVE_PRODUCTS_CACHE,
                self::CACHE_TTL,
                function () {
                    logger(
                        'Active Product Cache Warmed 🔥'
                    );

                    return Product::disableCache()
                        ->where('status', 1)
                        ->get();
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------------------
        */

        $warmedAt = now();

        $expiresAt =
            now()->addSeconds(
                self::CACHE_TTL
            );

        Cache::forever(
            self::CACHE_WARMED_AT,
            $warmedAt->format(
                'Y-m-d H:i:s'
            )
        );

        Cache::forever(
            self::CACHE_EXPIRES_AT,
            $expiresAt->timestamp
        );

        $this->registerCacheKey(
            self::ACTIVE_PRODUCTS_CACHE
        );

        return redirect('/cache-management')
            ->with(
                'success',
                $products->count() .
                ' active products cached successfully for ' .
                self::CACHE_TTL .
                ' seconds.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Cache
    |--------------------------------------------------------------------------
    */

    public function refreshCache()
    {
        $this->clearRegisteredCaches();

        $products =
            Cache::remember(
                self::ACTIVE_PRODUCTS_CACHE,
                self::CACHE_TTL,
                function () {
                    return Product::disableCache()
                        ->where('status', 1)
                        ->get();
                }
            );

        $warmedAt = now();

        $expiresAt =
            now()->addSeconds(
                self::CACHE_TTL
            );

        Cache::forever(
            self::CACHE_WARMED_AT,
            $warmedAt->format(
                'Y-m-d H:i:s'
            )
        );

        Cache::forever(
            self::CACHE_EXPIRES_AT,
            $expiresAt->timestamp
        );

        $this->registerCacheKey(
            self::ACTIVE_PRODUCTS_CACHE
        );

        return redirect('/cache-management')
            ->with(
                'success',
                'Cache refreshed successfully with the latest database data.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Clear All Cache Keys
    |--------------------------------------------------------------------------
    */

    public function clearAllProductCaches()
    {
        $this->clearRegisteredCaches();

        return redirect('/cache-management')
            ->with(
                'success',
                'All registered product and search caches have been cleared.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Product Search & Filtering
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $search =
            $request->input('search');

        $status =
            $request->input('status');

        $minPrice =
            $request->input('min_price');

        $maxPrice =
            $request->input('max_price');

        if ($status === 'all') {
            $status = null;
        }

        $filters = [
            'search' =>
                $search,

            'status' =>
                $status,

            'min_price' =>
                $minPrice,

            'max_price' =>
                $maxPrice,
        ];

        $cacheKey =
            'products_search_' .
            md5(
                json_encode($filters)
            );

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

        $products =
            Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use (
                    $search,
                    $status,
                    $minPrice,
                    $maxPrice
                ) {
                    $query =
                        Product::disableCache();

                    if (
                        $search !== null &&
                        $search !== ''
                    ) {
                        $query->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );
                    }

                    if (
                        $status !== null &&
                        $status !== ''
                    ) {
                        $query->where(
                            'status',
                            $status
                        );
                    }

                    if (
                        $minPrice !== null &&
                        $minPrice !== ''
                    ) {
                        $query->where(
                            'price',
                            '>=',
                            $minPrice
                        );
                    }

                    if (
                        $maxPrice !== null &&
                        $maxPrice !== ''
                    ) {
                        $query->where(
                            'price',
                            '<=',
                            $maxPrice
                        );
                    }

                    return $query
                        ->orderBy(
                            'id',
                            'asc'
                        )
                        ->get();
                }
            );

        $this->registerCacheKey(
            $cacheKey
        );

        return view(
            'product-search',
            [
                'products' =>
                    $products,

                'search' =>
                    $search,

                'status' =>
                    $status,

                'minPrice' =>
                    $minPrice,

                'maxPrice' =>
                    $maxPrice,

                'cacheHit' =>
                    $cacheHit,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    public function exportCsv(Request $request)
    {
        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $status =
            $request->input('status');

        $minPrice =
            $request->input('min_price');

        $maxPrice =
            $request->input('max_price');

        if (
            $status === 'all' ||
            $status === ''
        ) {
            $status = null;
        }

        $query =
            Product::disableCache();

        if ($search !== '') {
            $query->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        if ($status !== null) {
            $query->where(
                'status',
                $status
            );
        }

        if (
            $minPrice !== null &&
            $minPrice !== ''
        ) {
            $query->where(
                'price',
                '>=',
                $minPrice
            );
        }

        if (
            $maxPrice !== null &&
            $maxPrice !== ''
        ) {
            $query->where(
                'price',
                '<=',
                $maxPrice
            );
        }

        $products =
            $query
                ->orderBy(
                    'id',
                    'asc'
                )
                ->get();

        $filename =
            'products-' .
            now()->format(
                'Y-m-d-H-i-s'
            ) .
            '.csv';

        $headers = [
            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Content-Disposition' =>
                'attachment; filename="' .
                $filename .
                '"',
        ];

        $callback =
            function () use ($products) {
                $file =
                    fopen(
                        'php://output',
                        'w'
                    );

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                */

                fwrite(
                    $file,
                    "\xEF\xBB\xBF"
                );

                fputcsv(
                    $file,
                    [
                        'ID',
                        'Product Name',
                        'Price',
                        'Status',
                        'Created At',
                    ]
                );

                foreach ($products as $product) {
                    fputcsv(
                        $file,
                        [
                            $product->id,
                            $product->name,
                            $product->price,
                            $product->status
                                ? 'Available'
                                : 'Out of Stock',
                            $product->created_at,
                        ]
                    );
                }

                fclose($file);
            };

        return Response::stream(
            $callback,
            200,
            $headers
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Statistics
    |--------------------------------------------------------------------------
    */

    public function cacheStatistics()
    {
        $totalRequests =
            (int) Cache::get(
                'model_cache_total_requests',
                0
            );

        $hits =
            (int) Cache::get(
                'model_cache_hits',
                0
            );

        $misses =
            (int) Cache::get(
                'model_cache_misses',
                0
            );

        $productHits =
            (int) Cache::get(
                'model_cache_product_hits',
                0
            );

        $productMisses =
            (int) Cache::get(
                'model_cache_product_misses',
                0
            );

        $searchHits =
            (int) Cache::get(
                'model_cache_search_hits',
                0
            );

        $searchMisses =
            (int) Cache::get(
                'model_cache_search_misses',
                0
            );

        $databaseRequests =
            (int) Cache::get(
                'model_cache_database_requests',
                0
            );

        $hitRate = 0;

        $missRate = 0;

        if ($totalRequests > 0) {
            $hitRate =
                ($hits / $totalRequests) * 100;

            $missRate =
                ($misses / $totalRequests) * 100;
        }

        $totalProductRequests =
            $productHits +
            $productMisses;

        $productHitRate = 0;

        if ($totalProductRequests > 0) {
            $productHitRate =
                (
                    $productHits /
                    $totalProductRequests
                ) * 100;
        }

        $totalSearchRequests =
            $searchHits +
            $searchMisses;

        $searchHitRate = 0;

        if ($totalSearchRequests > 0) {
            $searchHitRate =
                (
                    $searchHits /
                    $totalSearchRequests
                ) * 100;
        }

        return view(
            'cache-statistics',
            [
                'totalRequests' =>
                    $totalRequests,

                'hits' =>
                    $hits,

                'misses' =>
                    $misses,

                'hitRate' =>
                    round(
                        $hitRate,
                        2
                    ),

                'missRate' =>
                    round(
                        $missRate,
                        2
                    ),

                'productHits' =>
                    $productHits,

                'productMisses' =>
                    $productMisses,

                'productHitRate' =>
                    round(
                        $productHitRate,
                        2
                    ),

                'searchHits' =>
                    $searchHits,

                'searchMisses' =>
                    $searchMisses,

                'searchHitRate' =>
                    round(
                        $searchHitRate,
                        2
                    ),

                'databaseRequests' =>
                    $databaseRequests,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Cache Statistics
    |--------------------------------------------------------------------------
    */

    public function resetCacheStatistics()
    {
        $this->resetStatisticsWithoutRedirect();

        return redirect('/cache-statistics')
            ->with(
                'success',
                'Cache statistics have been reset successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Internal Statistics Reset
    |--------------------------------------------------------------------------
    */

    private function resetStatisticsWithoutRedirect(): void
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
    }

    /*
    |--------------------------------------------------------------------------
    | Store New Product & Invalidate Cache
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => (int) $request->status,
        ]);

        $this->clearRegisteredCaches();

        return redirect('/products')
            ->with(
                'success',
                "Product '{$product->name}' created successfully and Model Cache auto-invalidated! ⚡"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product & Invalidate Cache
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'status' => (int) $request->status,
        ]);

        $this->clearRegisteredCaches();

        return redirect('/products')
            ->with(
                'success',
                "Product '{$product->name}' updated successfully and Model Cache auto-invalidated! ⚡"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Dynamic Cache TTL Studio
    |--------------------------------------------------------------------------
    */

    public function updateTtl(Request $request)
    {
        $request->validate([
            'ttl' => 'required|integer|min:1',
        ]);

        $ttl = (int) $request->ttl;
        Cache::forever('model_cache_dynamic_ttl', $ttl);

        $this->clearRegisteredCaches();

        return redirect('/cache-management')
            ->with(
                'success',
                "Dynamic Model Cache TTL updated to {$ttl} seconds successfully! 🎛"
            );
    }
}