<?php

beforeEach(function () {
    $this->category = \App\Models\Category::factory()->create();
    $this->data = ['name' => fake()->unique()->name()];
});


test('admin can view paginated categories list', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/categories?per_page=2');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'categories' => [
                    'data' => [
                        '*' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
                    ]
                ]
            ]
        ]);
});

test('normal user can view all categories (no pagination)', function () {
    $response = $this->getJson('/api/categories');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'categories' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
                ]
            ]
        ]);
});


test('admin can create a category', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/categories', $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'category' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
            ]
        ]);
});

test('cannot create category with duplicate', function () {
    $response = $this->postJson('/api/categories', $this->data);
});


test('user can view specific category', function () {
    $response = $this->getJson("/api/categories/{$this->category->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'category' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
            ]
        ]);
});


test('admin can update category name', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/categories/{$this->category->id}", $this->data);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'category' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
            ]
        ]);
});


test('admin can delete a category', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/categories/{$this->category->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'category' => ['id', 'name', 'created_at', 'updated_at', 'products_count']
            ]
        ]);
});
