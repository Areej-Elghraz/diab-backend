<?php

use App\Enums\PhoneNumberTypeEnum;

beforeEach(function () {
    $this->phoneNumber = \App\Models\PhoneNumber::factory()->create();
    $this->data = [
        'phone' => '+201012345678',
        'type' => fake()->randomElements(PhoneNumberTypeEnum::values(), rand(1, 2)),
    ];
});

test('user can view all phone numbers (no pagination)', function () {
    $response = $this->getJson('/api/phone-numbers');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'phone_numbers' => [
                    '*' => ['id', 'phone', 'type', 'created_at', 'updated_at']
                ]
            ]
        ]);
});


test('admin can create a phone number', function () {
    $response = $this->withHeaders(headers: [
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/phone-numbers', $this->data);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'phone_number' => ['id', 'phone', 'type', 'created_at', 'updated_at']
            ]
        ]);
});

test('cannot create phone number with duplicate number', function () {
    $response = $this->withHeaders(headers: [
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/phone-numbers', [
        'phone' => $this->phoneNumber->phone,
        'type' => $this->phoneNumber->type,
    ]);
});


test('user can view specific phone number', function () {
    $response = $this->getJson("/api/phone-numbers/{$this->phoneNumber->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'phone_number' => ['id', 'phone', 'type', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can update phone number', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->putJson("/api/phone-numbers/{$this->phoneNumber->id}", $this->data);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'phone_number' => ['id', 'phone', 'type', 'created_at', 'updated_at']
            ]
        ]);
});


test('admin can delete a phone number', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->deleteJson("/api/phone-numbers/{$this->phoneNumber->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'phone_number' => ['id', 'phone', 'type', 'created_at', 'updated_at']
            ]
        ]);
});
