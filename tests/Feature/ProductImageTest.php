<?php

use App\Enums\ProductImagePositionEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->category = \App\Models\Category::factory()->create();
    $this->product = \App\Models\Product::factory()->create(['category_id' => $this->category->id]);
    $this->data = [
        'images' => [
            UploadedFile::fake()->image('before.jpg'),
            UploadedFile::fake()->image('gallery.jpg'),
        ],
        'positions' => ['before', 'gallery'],
        'image' => UploadedFile::fake()->image('updated.jpg'),
        'position' => 'gallery',
    ];
    $this->product->images()->createMany([
        ['image' => 'fake3.jpg', 'position' => 'gallery'],
        ['image' => 'fake4.jpg', 'position' => 'before'],
        ['image' => 'fake5.jpg', 'position' => 'after'],
    ]);

    $this->product->load('images', 'gallery', 'beforeImage', 'afterImage');
});

test('user can view all product images', function () {
    $response = $this->getJson("/api/products/{$this->product->id}/images");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'images' => [
                    '*' => ['id', 'image', 'position', 'created_at', 'updated_at']
                ]
            ]
        ]);
});

test('admin can add new images to product', function () {
    Storage::fake('public');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson("/api/products/{$this->product->id}/images", $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'images' => [
                    '*' => ['id', 'image', 'position', 'created_at', 'updated_at']
                ]
            ]
        ]);


    /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
    $storage = Storage::disk('public');
    $storage->assertExists('product_images/' . basename($this->data['images'][0]->hashName()));
});

test('user can view a single product image', function () {
    $image = $this->product->images()->first();

    $response = $this->getJson("/api/products/{$this->product->id}/images/{$image->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'image' => ['id', 'image', 'position', 'created_at', 'updated_at']
            ]
        ]);
});

test('admin can update a product image', function () {
    Storage::fake('public');
    $image = $this->product->gallery()?->first();

    if ($image) {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->putJson("/api/products/{$this->product->id}/images/{$image->id}", $this->data);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'image' => ['id', 'image', 'position', 'created_at', 'updated_at']
                ]
            ]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists('product_images/' . basename($this->data['image']->hashName()));
    }
});

test('admin can delete a product image', function () {
    $image = $this->product->gallery()?->first();

    if ($image) {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->deleteJson("/api/products/{$this->product->id}/images/{$image->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'image' => ['id', 'image', 'position', 'created_at', 'updated_at']
                ]
            ]);
    }
});

test('admin can delete all product images', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/products/{$this->product->id}/images");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'images' => [
                    '*' => ['id', 'image', 'position', 'created_at', 'updated_at']
                ]
            ]
        ]);
});
