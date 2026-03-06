<?php

namespace App\Auth\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['pgsql'];


    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => '123123',
            'password_confirmation' => '123123'
        ]);

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                        'updated_at',
                    ],
                    'token'
            ]);
    }


    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '456'
        ]);

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name', 
                    'email', 
                    'password'
            ]]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $password = fake()->password(8);
        $user =  User::factory()->create([
            'password' => $password,
        ]);
 
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 
            'password' => $password,
        ]);

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'token',
            ]);
    }


    public function test_login_fails_with_invalid_credentials(): void
    {
        $user =  User::factory()->create();
 
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => fake()->email, 
            'password' => fake()->password(8),
        ]);

        $response
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'error',
            ]);
    }
}

