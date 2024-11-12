<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    protected $adminUser;
    protected $User;
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role_id' => 1]);
        $this->User = User::factory()->create(['role_id' => 2]);

    }


    /** @test */

    public function Unauthorized_for_you()
    {
        $users = User::select('name', 'id')->get();

        $response = $this->actingAs($this->User)->get('/api/user');
        $response->assertStatus(403);
        $response->assertJson([
                "message" => 'Unauthorized'
            ]);
    }

    /** @test */
    public function it_can_show_all_users()
    {
        $users = User::select('name', 'id')->get();

        $response = $this->actingAs($this->adminUser)->get('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'بيانات المستخدمين',
            'data' => $users->toArray(),
        ]);
    }


    /** @test */
    public function it_can_store_a_user()
    {
        $userData = User::factory()->make(['role_id' => 1])->toArray();
        $userData['password'] = 'securepassword123';

        $response = $this->actingAs($this->adminUser)->post('/api/user', $userData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'تم إنشاء الحساب',
            'data' => [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);
    }

    /** @test */
    public function it_returns_error_creat_user_due_to_validation()
    {


        // Create updated data without required fields
        $updatedData = User::factory()->make(['name' =>null])->toArray();

        $response = $this->actingAs($this->adminUser)->post('/api/user/', $updatedData);

        // Assert: Check that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Assert: Check that validation errors contain the 'name' field
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_returns_error_creating_user()
    {
        //Create incomplete data to trigger validation error
        $userData = User::factory()->make(['password' => null])->toArray();

        $response = $this->actingAs($this->adminUser)->post('/api/user', $userData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ],
        ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = User::factory()->create(['role_id' => 1]);
        $updatedData = User::factory()->make()->toArray();

        $response = $this->actingAs($this->adminUser)->put('/api/user/' . $user->id, $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'تمت عملية التحديث',
            'data' => [
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
            ],
        ]);

        // Check that the user is updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /** @test */
    public function it_returns_error_updating_user_user_not_found()
    {
        $response = $this->actingAs($this->adminUser)->put('/api/user/9999', []); // Assuming 9999 does not exist

        $response->assertStatus(404);
        $response->assertJson([
         'message' => 'المستخدم غير موجود',
             'data' => 'لا يوجد بيانات',
         ]);
    }




    /** @test */
    public function it_can_delete_a_user()
    {
        $user = User::factory()->create(['role_id' => 2]);

        $response = $this->actingAs($this->adminUser)->delete('/api/user/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'تمت عملية الحذف']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_returns_error_deleting_user()
    {
        $response = $this->actingAs($this->adminUser)->delete('/api/user/9999'); // Assuming 9999 does not exist

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'المستخدم غير موجود',
        ]);
    }

    /** @test */
    public function it_can_show_a_user()
    {
        $user = User::factory()->create(['role_id' => 1]);

        $response = $this->actingAs($this->adminUser)->get('/api/user/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'بيانات المستخدم',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
    public function it_returns_error_showing_user()
    {
        $response = $this->actingAs($this->adminUser)->get('/api/user/9999', []); // Assuming 9999 does not exist

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'المستخدم غير موجود',
            'data' => 'لا يوجد بيانات',
        ]);
    }
}
