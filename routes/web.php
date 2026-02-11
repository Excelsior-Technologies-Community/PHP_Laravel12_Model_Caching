<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Seed sample products into the database
Route::get('/seed', [ProductController::class, 'seed']);

// Display cached product list
Route::get('/products', [ProductController::class, 'index']);

// Display products without using cache (for comparison)
Route::get('/products-no-cache', [ProductController::class, 'withoutCache']);

// Delete a product by ID and clear cache
Route::get('/delete/{id}', [ProductController::class, 'delete']);
