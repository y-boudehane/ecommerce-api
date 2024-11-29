<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */

    public function it_can_list_products()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);

        // Arrange
        Product::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function it_can_create_a_product()
    {
        $data = [
            'name' => 'New Product',
            'description' => 'Product description',
            'categories' => [1, 2],
            'price' => 10.99,
            'stock' => 100,
        ];

        $product = Product::create($data);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'categories' => [1, 2],
            'price' => 10,
            'stock' => 10,
        ];

        $product->update($data);

        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $product->delete();

        $this->assertSoftDeleted($product);
    }

      /** @test */
    public function it_can_search_products_by_name_or_description()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);

        // Arrange
        Product::factory()->create(['name' => 'Special Product']);
        Product::factory()->create(['description' => 'Special Description']);

        // Act
        $response = $this->getJson('/api/search/products?query=Special');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
}
