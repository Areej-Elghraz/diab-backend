<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->product = Product::factory()->create(['category_id' => $this->category->id]);
    
    $this->data = [
        'name' => fake()->unique()->name(),
        'description' => 'ركنة: 4 فتيه و 1 كنبة و 1 ترابيزة',
        'price' => 150.50,
        'category_id' => $this->category->id,
        'images' => [
            UploadedFile::fake()->image('product1.jpg'),
            UploadedFile::fake()->image('product2.jpg'),
            UploadedFile::fake()->image('product3.jpg'),
        ],
        'positions' => [
            'before',
            'after',
            'gallery',
        ],
    ];
});

test('user can view all paginated products list', function () {
    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'products' => [
                    'data' => [
                        '*' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
                    ]
                ]
            ]
        ]);
});

test('admin can view trashed products', function () {
    $product = Product::factory()->create();
    $product->delete();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->getJson('/api/products/trashed');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'products' => [
                    'data' => [
                        '*' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
                    ]
                ]
            ]
        ]);
});

test('admin can create a product', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/products', $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can view single product', function () {
    $product = Product::factory()->create();
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->getJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);
});

test('admin can view single trashed product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->getJson("/api/products/trashed/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);
});

test('admin can update product', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/products/{$this->product->id}", $this->data);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);
});

test('admin can soft delete a product', function () {
    $product = Product::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});


test('admin can restore a trashed product', function () {
    $product = Product::factory()->create();
    $product_id = $product->id;
    $product->delete();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/products/restore/{$product_id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'deleted_at' => null,
    ]);
});

test('admin can permanently delete a product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/products/force-delete/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at']
            ]
        ]);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
