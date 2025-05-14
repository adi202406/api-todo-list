<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FcmTokenTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();

        // Generate token for the user
        $this->token = $this->user->createToken('TestToken')->plainTextToken;

        // Authenticate with the token
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);
    }

    /** @test */
    public function it_can_save_fcm_token()
    {
        $response = $this->postJson('/api/fcm-token', [
            'device_token' => 'test_device_token',
            'device_type' => 'android'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Device token saved successfully'
            ]);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $this->user->id,
            'device_token' => 'test_device_token'
        ]);
    }

    /** @test */
    public function it_can_update_existing_fcm_token()
    {
        UserDevice::factory()->create([
            'user_id' => $this->user->id,
            'device_token' => 'existing_token',
            'device_type' => 'ios'
        ]);

        $response = $this->postJson('/api/fcm-token', [
            'device_token' => 'existing_token',
            'device_type' => 'android'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $this->user->id,
            'device_token' => 'existing_token',
            'device_type' => 'android'
        ]);
    }

    /** @test */
    public function it_can_remove_fcm_token()
    {
        $device = UserDevice::factory()->create([
            'user_id' => $this->user->id,
            'device_token' => 'token_to_delete'
        ]);

        $response = $this->deleteJson('/api/fcm-token/token_to_delete');

        $response->assertJson([
            'success' => true,
            'message' => 'Device token removed successfully'
        ]);

        $this->assertDatabaseMissing('user_devices', ['id' => $device->id]);
    }
}