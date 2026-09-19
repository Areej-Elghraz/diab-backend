<?php

use App\Enums\SocialMediaEnum;

beforeEach(function () {
    $this->socialLink = \App\Models\SocialLink::factory()->create();
    $this->data = [
        'url' => fake()->url(),
        'social_media' => fake()->randomElement(SocialMediaEnum::values())
    ];
});

test('user can view all social links (no pagination)', function () {
    $response = $this->getJson('/api/social-links');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'social_links' => [
                    '*' => ['id', 'url', 'social_media', 'created_at', 'updated_at']
                ]
            ]
        ]);
});


test('admin can create a social link', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/social-links', $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'social_link' => ['id', 'url', 'social_media', 'created_at', 'updated_at']
            ]
        ]);
});


test('user can view specific social link', function () {
    $response = $this->getJson("/api/social-links/{$this->socialLink->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'social_link' => ['id', 'url', 'social_media', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can update social link', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/social-links/{$this->socialLink->id}", $this->data);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'social_link' => ['id', 'url', 'social_media', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can delete a social link', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/social-links/{$this->socialLink->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'social_link' => ['id', 'url', 'social_media', 'created_at', 'updated_at']
            ]
        ]);
});
