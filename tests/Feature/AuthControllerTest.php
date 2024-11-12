<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{

    /** @test */

    public function it_can_login()
    {
        // Create a user
        $user = User::factory()->create(['role_id' => 1]);

        // Simulate login with valid credentials
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',  // Ensure this matches the password used in factory
        ]);
        // Check if the response status is correct
        $response->assertStatus(200);  // Should match the status in your controller
        // Check if the response JSON contains the expected data
        $response->assertJson([
            'message' => 'Successfully logged in',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,

            ]
        ]);

        // Check that the token is included
        $response->assertJsonStructure([
            'authorisation' => [
                'token',
                'type',
            ]
        ]);
    }
    /** @test */
    public function acccount_not_exists()
    {
        $response = $this->postJson('/api/login', [
                'email' => 'aliahgmad@gmail.com',
                'password' => 'password',  // Ensure this matches the password used in factory
            ]);
        // Check if the response status is correct
        $response->assertStatus(401);  // Should match the status in your controller

        // Check if the response JSON contains the expected data
        $response->assertJson([
                  'message' => 'لا يوجد حساب',
              'data' => 'لا يوجد بيانات',
        ]);
    }


    /** @test */
    public function it_can_logout()
    {//creat user
        $user = User::factory()->create(['role_id' => 1]);
        // Simulate login to get the token
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        // Extract token from the login response
        $token = $response->json('token');
        // Simulate logout with the token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');
        // Check if the response status is correct
        $response->assertStatus(200);
        // Check if the response JSON contains the expected message
        $response->assertJsonPath('message', "تم تسجيل الخروج");
    }


}
