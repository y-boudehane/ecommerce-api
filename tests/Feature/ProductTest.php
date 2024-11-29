<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products()
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

    public function test_can_store_product_with_categories()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        // Arrange
        $categories = Category::factory()->count(2)->create();
        $data = [
            'name' => 'New Product',
            'description' => 'Product Description',
            'price' => 50.99,
            'stock' => 20,
            'categories' => $categories->pluck('id')->toArray()
        ];

        // Act
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
        $this->assertEquals(2, Product::first()->categories()->count());
    }

    public function test_can_update_product()
    {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        // Arrange
        $product = Product::factory()->create();
        $updatedData = ['name' => 'Updated Product Name'];

        // Act
        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product Name']);
    }

    public function test_can_delete_product()
    {

        Sanctum::actingAs(User::factory()->create(), ['*']);

        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_search_products_by_name_or_description()
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
