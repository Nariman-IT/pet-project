<?php

declare(strict_types=1);

namespace App\Auth\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected array $connectionsToTransact = ['pgsql'];

    public function testUserCanRegisterWithValidData(): void
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => '123123',
            'password_confirmation' => '123123',
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_CREATED)
            ->assertJsonStructure(structure: [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ]);
    }

    public function testRegistrationFailsWithInvalidData(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(structure: [
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                ]]);
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $password = fake()->password(8);
        $user =  User::factory()->create(attributes: [
            'password' => $password,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_OK)
            ->assertJsonStructure(structure: [
                'token',
            ]);
    }

    public function testLoginFailsWithInvalidCredentials(): void
    {
        $user =  User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => fake()->email,
            'password' => fake()->password(8),
        ]);

        $response
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertStatus(status: Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure(structure: [
                'error',
            ]);
    }
}
