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

Route::get('/products', [
    ProductController::class,
    'index'
]);

Route::get('/products-no-cache', [
    ProductController::class,
    'withoutCache'
]);

Route::get('/delete/{id}', [
    ProductController::class,
    'delete'
]);


/*
|--------------------------------------------------------------------------
| Cache Performance Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/cache-performance', [
    ProductController::class,
    'performance'
]);


/*
|--------------------------------------------------------------------------
| Cache Management Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/cache-management', [
    ProductController::class,
    'cacheManagement'
]);

Route::get('/cache-management/clear', [
    ProductController::class,
    'clearCache'
]);

Route::get('/cache-management/warm', [
    ProductController::class,
    'warmCache'
]);


/*
|--------------------------------------------------------------------------
| Cached Product Search & Filtering
|--------------------------------------------------------------------------
*/

Route::get('/products/search', [
    ProductController::class,
    'search'
]);


/*
|--------------------------------------------------------------------------
| Cache Statistics & Analytics
|--------------------------------------------------------------------------
*/

Route::get('/cache-statistics', [
    ProductController::class,
    'cacheStatistics'
]);

Route::get('/cache-statistics/reset', [
    ProductController::class,
    'resetCacheStatistics'
]);