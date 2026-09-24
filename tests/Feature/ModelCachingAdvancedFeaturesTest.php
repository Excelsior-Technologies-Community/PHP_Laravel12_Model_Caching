<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ModelCachingAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_listing_returns_cache_hit_and_execution_latency(): void
    {
        Product::create([
            'name' => 'Testing Phone X',
            'price' => 49999,
            'status' => 1,
        ]);

        // First request - Cache Miss
        $response1 = $this->get('/products');
        $response1->assertStatus(200);
        $response1->assertSee('CACHE MISS');
        $response1->assertSee('Testing Phone X');

        // Second request - Cache Hit
        $response2 = $this->get('/products');
        $response2->assertStatus(200);
        $response2->assertSee('CACHE HIT');
    }

    public function test_storing_new_product_auto_invalidates_registered_cache(): void
    {
        // Populate cache
        $this->get('/products');

        $response = $this->post('/products', [
            'name' => 'Auto Invalidation Phone',
            'price' => 59999,
            'status' => 1,
        ]);

        $response->assertRedirect('/products');
        $response->assertSessionHas('success');

        // Subsequent request should be CACHE MISS because cache was auto-invalidated
        $subsequentResponse = $this->get('/products');
        $subsequentResponse->assertSee('CACHE MISS');
        $subsequentResponse->assertSee('Auto Invalidation Phone');
    }

    public function test_updating_product_auto_invalidates_registered_cache(): void
    {
        $product = Product::create([
            'name' => 'Old Phone Name',
            'price' => 29999,
            'status' => 1,
        ]);

        // Warm cache
        $this->get('/products');

        $response = $this->post("/products/{$product->id}/update", [
            'name' => 'Updated Super Phone',
            'price' => 34999,
            'status' => 1,
        ]);

        $response->assertRedirect('/products');
        $response->assertSessionHas('success');

        $subsequentResponse = $this->get('/products');
        $subsequentResponse->assertSee('CACHE MISS');
        $subsequentResponse->assertSee('Updated Super Phone');
    }

    public function test_updating_dynamic_cache_ttl(): void
    {
        $response = $this->post('/cache-management/ttl', [
            'ttl' => 3600,
        ]);

        $response->assertRedirect('/cache-management');
        $response->assertSessionHas('success');

        $this->assertEquals(3600, Cache::get('model_cache_dynamic_ttl'));
    }
}
