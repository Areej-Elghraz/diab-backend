<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('user can login with correct credentials', function () {
    $response = $this->postJson('/api/login', [
        // 'input'    => $this->admin->adminname,
        'input'    => $this->admin->email,
        'password' => 'ayadiab',
        'remember' => $this->remember,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'name', 'username', 'email'],
                'access_token',
                'remember_token',
            ],
        ]);
});

test('user can log out', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->accessToken,
    ])->postJson('/api/logout');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [],
        ]);
});

test('user may forget password', function () {
    $response = $this->postJson('/api/forget-password', [
        // 'input' => $this->admin->adminname,
        'input' => $this->admin->email,
        'url'   => 'https://ayadiab-431.github.io/Diab/products',
    ]);

    $response->assertOk()
        ->assertJsonStructure(
            [
                'message',
                'data' => [],
            ]
        );
});

test('user may need to resend otp', function () {
    $response = $this->postJson('/api/resend-otp', data: [
        // 'input' => $this->admin->adminname,
        'input' => $this->admin->email,
        'url'   => 'https://ayadiab-431.github.io/Diab/products',
    ]);

    $response->assertOk()
        ->assertJsonStructure(
            [
                'message',
                'data' => [],
            ]
        );
});

test('user can reset password', function () {
    $otp = 123123;
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->admin->email],
        [
            'token' => Hash::make($otp),
            'verified' => true,
            'created_at' => now(),
        ]
    );

    $response = $this->postJson('/api/reset-password', data: [
        // 'input'                     => $this->admin->adminname,
        'input'                     => $this->admin->email,
        'otp'                       => (string) $otp,
        'new_password'              => 'ayadiab123',
        'new_password_confirmation' => 'ayadiab123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(
            [
                'message',
                'data' => [],
            ]
        )
    ;
});

test('user can refresh access token using remember token', function () {
    if ($this->remember) {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->rememberToken,
        ])->postJson('/api/refresh-token');
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'message',
                    'data' => [],
                ]
            )
        ;
    }
});
