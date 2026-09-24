<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Product & Model Caching Routes
|--------------------------------------------------------------------------
*/

Route::get('/seed', [
    ProductController::class,
    'seed'
]);

/*
|--------------------------------------------------------------------------
| Main Product Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/products', [
    ProductController::class,
    'index'
])->name('products.index');

/*
|--------------------------------------------------------------------------
| Non-Cached Comparison
|--------------------------------------------------------------------------
*/

Route::get('/products-no-cache', [
    ProductController::class,
    'withoutCache'
])->name('products.no-cache');

/*
|--------------------------------------------------------------------------
| Single Product Delete
|--------------------------------------------------------------------------
*/

Route::get('/delete/{id}', [
    ProductController::class,
    'delete'
])->name('products.delete');

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

Route::post('/products/bulk-delete', [
    ProductController::class,
    'bulkDelete'
])->name('products.bulk-delete');

/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get('/products/export', [
    ProductController::class,
    'exportCsv'
])->name('products.export');

/*
|--------------------------------------------------------------------------
| Cached Product Search
|--------------------------------------------------------------------------
*/

Route::get('/products/search', [
    ProductController::class,
    'search'
])->name('products.search');

/*
|--------------------------------------------------------------------------
| Cache Performance Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/cache-performance', [
    ProductController::class,
    'performance'
])->name('cache.performance');

/*
|--------------------------------------------------------------------------
| Cache Management Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/cache-management', [
    ProductController::class,
    'cacheManagement'
])->name('cache.management');

/*
|--------------------------------------------------------------------------
| Clear Cache
|--------------------------------------------------------------------------
*/

Route::get('/cache-management/clear', [
    ProductController::class,
    'clearCache'
])->name('cache.clear');

/*
|--------------------------------------------------------------------------
| Warm Cache
|--------------------------------------------------------------------------
*/

Route::get('/cache-management/warm', [
    ProductController::class,
    'warmCache'
])->name('cache.warm');

/*
|--------------------------------------------------------------------------
| Refresh Cache
|--------------------------------------------------------------------------
*/

Route::get('/cache-management/refresh', [
    ProductController::class,
    'refreshCache'
])->name('cache.refresh');

/*
|--------------------------------------------------------------------------
| Clear All Registered Product/Search Caches
|--------------------------------------------------------------------------
*/

Route::get('/cache-management/clear-all', [
    ProductController::class,
    'clearAllProductCaches'
])->name('cache.clear-all');

/*
|--------------------------------------------------------------------------
| Cache Statistics
|--------------------------------------------------------------------------
*/

Route::get('/cache-statistics', [
    ProductController::class,
    'cacheStatistics'
])->name('cache.statistics');

/*
|--------------------------------------------------------------------------
| Store Product (Triggers Cache Auto-Invalidation)
|--------------------------------------------------------------------------
*/

Route::post('/products', [
    ProductController::class,
    'store'
])->name('products.store');

/*
|--------------------------------------------------------------------------
| Update Product (Triggers Cache Auto-Invalidation)
|--------------------------------------------------------------------------
*/

Route::post('/products/{id}/update', [
    ProductController::class,
    'update'
])->name('products.update');

/*
|--------------------------------------------------------------------------
| Reset Statistics
|--------------------------------------------------------------------------
*/

Route::get('/cache-statistics/reset', [
    ProductController::class,
    'resetCacheStatistics'
])->name('cache.statistics.reset');

/*
|--------------------------------------------------------------------------
| Dynamic Cache TTL Control Studio
|--------------------------------------------------------------------------
*/

Route::post('/cache-management/ttl', [
    ProductController::class,
    'updateTtl'
])->name('cache.update-ttl');