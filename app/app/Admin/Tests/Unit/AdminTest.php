<?php

declare(strict_types=1);

namespace App\Admin\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected array $connectionsToTransact = ['pgsql'];
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regularUser = User::factory()->create();
    }
  
    public function test_admin_can_get_users_list(): void
    {
        User::factory()->count(15)->create();
        $response = $this->getJson('/api/v1/admin/users?page=2');
        $response->assertStatus(200)
            ->assertHeader(headerName: 'Content-Type', value: 'application/json')
            ->assertJsonStructure([
                'users' => [[
                            'id',
                            'name',
                            'email',
                            'role',
                            'created_at',
                            'updated_at'
                        ]]
                ]);
    }


    public function test_validation_fails_with_invalid_role(): void
    {
        $response = $this->postJson("/api/v1/admin/{$this->regularUser->id}/update", [
                'role' => 'superadmin'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_admin_cannot_update_nonexistent_user(): void
    {
        $response = $this->postJson('/api/v1/admin/99999/update', [
                'role' => 'admin'
            ]);

        $response->assertStatus(404);
    }

    public function test_admin_can_update_user_role(): void
    {
        $userToUpdate = User::factory()->create();
        
        $response = $this->postJson("/api/v1/admin/{$userToUpdate->id}/update", [
                'role' => 'admin'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $userToUpdate->id,
            'role' => 'admin'
        ]);
    }
}
