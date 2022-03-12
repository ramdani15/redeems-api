<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get current's user profile
     */
    public function test_get_profile()
    {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)
                         ->get('/api/v1/profile');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'email' => $user->email,
                 ]);
    }

    /**
     * Update current's user profile
     */
    public function test_update_profile()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'update',
            'email' => 'update@mail.com'
        ];
        $response = $this->actingAs($user)
                         ->patchJson('/api/v1/profile', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => $data['name'],
                     'email' => $data['email'],
                 ]);
    }
}
