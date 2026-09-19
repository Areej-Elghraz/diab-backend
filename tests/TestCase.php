<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    public $admin, $remember = false, $accessToken, $rememberToken, $loginResponse, $data;

    protected function SetUp(): void
    {
        parent::SetUp();
        $this->remember = true;
        $this->admin    = User::factory()->create([
            'name'              => 'Admin',
            'username'          => 'ayadiab123',
            'email'             => 'diabfurnitures@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('ayadiab'),
            'role'              => 'admin',
        ]);

        $this->loginResponse = $this->postJson('/api/login', [
            // 'input'    => $this->admin->admin-name,
            'input'    => $this->admin->email,
            'password' => 'ayadiab',
            'remember' => $this->remember,
        ]);

        $this->loginResponse->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'username', 'email'],
                    'access_token',
                    'remember_token',
                ],
            ]);
        $this->accessToken   = $this->loginResponse->json('data.access_token');
        $this->rememberToken = $this->loginResponse->json('data.remember_token');

    }
}
