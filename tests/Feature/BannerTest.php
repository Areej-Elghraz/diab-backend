<?php

use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->banner = \App\Models\Banner::factory()->create();
    $this->data = [
        'images' => [UploadedFile::fake()->image('banner1.jpg')],
        'image' => UploadedFile::fake()->image('banner2.png'),
    ];
});

test('user can view all banners (no pagination)', function () {
    $response = $this->getJson('/api/banners');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'banners' => [
                    '*' => ['id', 'image', 'created_at', 'updated_at']
                ]
            ]
        ]);
});


test('admin can create a banner', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/banners', $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'banners' => [
                    '*' => ['id', 'image', 'created_at', 'updated_at']
                ]
            ]
        ]);
});


test('user can view specific banner', function () {
    $response = $this->getJson("/api/banners/{$this->banner->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'banner' => ['id', 'image', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can update banner', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/banners/{$this->banner->id}", $this->data);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'banner' => ['id', 'image', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can delete a banner', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/banners/{$this->banner->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'banner' => ['id', 'image', 'created_at', 'updated_at']
            ]
        ]);
});
