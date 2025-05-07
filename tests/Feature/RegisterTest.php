<?php

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration.
     */
    public function test_register_creates_user_and_returns_token()
    {
        // Prepare the request data
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // Confirm password for validation
        ];

        // Send a POST request to the registration route
        $response = $this->postJson('/api/auth/register', $requestData);

        // Assert response status
        $response->assertStatus(201);

        // Assert response structure
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
            ],
        ]);

        // Assert user is created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        // Assert password is hashed
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test registration fails with invalid data.
     */
    public function test_register_fails_with_invalid_data()
    {
        // Prepare invalid request data (missing required fields)
        $requestData = [
            'name' => 'John Doe',
            // Missing email, password, and username
        ];

        // Send a POST request to the registration route
        $response = $this->postJson('/api/auth/register', $requestData);

        // Assert response status
        $response->assertStatus(422); // Unprocessable Entity

        // Assert the response contains validation errors
        $response->assertJsonValidationErrors(['email', 'password']);

        // Assert user is not created in the database
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test registration fails with duplicate email.
     */
    public function test_register_fails_with_duplicate_email()
    {
        // Create an existing user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Prepare request data with duplicate email
        $requestData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com', // Duplicate email
            'password' => 'password123',
            'password_confirmation' => 'password123', // Confirm password for validation
        ];

        // Send a POST request to the registration route
        $response = $this->postJson('/api/auth/register', $requestData);

        // Assert response status
        $response->assertStatus(422); // Unprocessable Entity

        // Assert the response contains validation errors for the email
        $response->assertJsonValidationErrors(['email']);

        // Assert user is not created in the database
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
        ]);
    }
}
