<?php

namespace Tests\Feature;

use App\Models\Permission;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionControllerTest extends TestCase
{
    public $adminUser;
    protected $User;
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role_id' => 1]); // Create an admin user
        $this->User = User::factory()->create(['role_id' => 2]); // Create an  user

    }

    /** @test */

    public function Unauthorized_for_you()
    {

        $Permission=Permission::all()->select(['permission_name', 'description']);
        $response = $this->actingAs($this->User)->get('/api/permission');

        $response->assertJson([
                "message" => 'Unauthorized'

            ]);

    }

    /** @test */
    public function it_can_show_all_Permission()
    {
        $Permission=Permission::all()->select(['permission_name', 'description']);
        $response = $this->actingAs($this->adminUser)->get('/api/permission');
        $response->assertStatus(200);
        $response->assertJson([
                "message" => "All permission",
                "data" => $Permission->toArray(),
            ]);
    }

    /** @test */

    public function it_can_store_new_permission()
    {
        // Arrange: Create a permission data instance
        $permissionData = Permission::factory()->make()->toArray(); // Convert to array for posting

        // Act: Make a POST request to store the new permission
        $response = $this->actingAs($this->adminUser)->post('/api/permission', $permissionData);

        // Assert: Check that the response status is 201 (Created)
        $response->assertStatus(201);

        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Permission created successfully",
        ]);
        // Assert: Verify that the permission has been added to the database
        $this->assertDatabaseHas('permissions', [
            'permission_name' => $permissionData['permission_name'], // Ensure this matches the field in your permissions table
            'description' => $permissionData['description'], // Include other relevant fields
        ]);
    }




    /** @test */
    public function it_returns_validation_name_errors_name_not_existe_when_storing_a_new_permission()
    {
        // Act: Make a POST request with missing role_name
        $response = $this->actingAs($this->adminUser)->post('/api/permission', [
            'description' => 'No permission name provided',
        ]);

        // Assert: Check that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422);
        // Assert: Check that validation errors contain the 'role_name' field
        $response->assertJsonValidationErrors(['permission_name']);
    }

    /** @test */

    public function it_returns_validation_name_errors_when_storing_a_new_role()
    {
        // Arrange: Prepare the data for a new role
        $roleData =[
        'role_name'=>'user.index',
         'description'=>'very impotent description'
        ];
        $response = $this->actingAs($this->adminUser)->post('/api/permission', $roleData);
        $response->assertStatus(422);
        // Assert: Check that validation errors contain the 'role_name' field
        $response->assertJsonValidationErrors(['permission_name']);

    }




    /** @test */
    public function it_can_update_permission()
    {
        $permission=Permission::factory()->create();
        $Permission2=Permission::factory()->make();
        $response = $this->actingAs($this->adminUser)->put('/api/permission/'.$permission->id, $Permission2->toArray());
        $response->assertStatus(200);
        $response->assertJson([
                "message" => "Permission updated successfully",
            ]);
        $this->assertDatabaseHas('permissions', [
                    'permission_name' => $Permission2['permission_name'],
                    'description' => $Permission2['description'],
                ]);

    }

    /** @test */
    public function it_can_delete_permission()
    {
        // Arrange: Create a permission instance
        $permission = Permission::factory()->create();

        // Act: Make a DELETE request to remove the permission
        $response = $this->actingAs($this->adminUser)->delete('/api/permission/' . $permission->id);

        // Assert: Check that the response status is 204 (No Content)
        $response->assertStatus(204);

        // Assert: Verify that the permission has been removed from the database
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);



    }

    /** @test */
    public function it_can_show_permission()
    {
        // Arrange: Create a permission instance
        $permission = Permission::factory()->create();

        // Act: Make a DELETE request to remove the permission
        $response = $this->actingAs($this->adminUser)->get('/api/permission/' . $permission->id);

        // Assert: Check that the response status is 204 (No Content)
        $response->assertStatus(200);


        $this->assertDatabaseHas('permissions', [
                    'permission_name' => $permission['permission_name'],
                    'description' => $permission['description'],
                ]);


    }
    /** @test */

    public function it_returns_error_showing_permission_not_found()
    {

        // Act: Make a GET request to retrieve the role
        $response = $this->actingAs($this->adminUser)->get('/api/permission/333');
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "permission not found",
        ]);
    }

}
