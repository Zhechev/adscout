<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{

    public function test_registered_user_can_login()
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Check response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_user_can_logout()
    {
        // Create and authenticate user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Attempt logout
        $response = $this->postJson('/api/logout');

        // Check response status and content
        $response->assertStatus(204); // Using 204 if that is what your API returns
    }
}
