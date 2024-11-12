<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleControllerTest extends TestCase
{
    protected $adminUser;
    protected $User;
    /**
     * Set up the test environment.
     *
     * This method is called before each test. It initializes the admin user
     * and disables middleware to allow direct access to the API routes.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role_id' => 1]);
        $this->User = User::factory()->create(['role_id' => 2]); // Create an  user
    }


    /** @test */

    public function Unauthorized_for_you()
    {
        $rols=Role::all();
        $response = $this->actingAs($this->User)->get('/api/role');
        $response->assertStatus(403);
        $response->assertJson([
                "message" => 'Unauthorized'
            ]);
    }
    /** @test */

    public function it_can_show_all_rols()
    {
        $rols=Role::all();
        $response = $this->actingAs($this->adminUser)->get('/api/role');
        $response->assertStatus(200);
        $response->assertJson([
                "message" => "All roles",
                "data" => $rols->toArray(),
            ]);
    }

    /** @test */
    public function it_can_store_a_new_role()
    {
        // Arrange: Prepare the data for a new role
        $roleData = Role::factory()->make()->toArray();


        // Act: Make a POST request to create a new role
        $response = $this->actingAs($this->adminUser)->post('/api/role', $roleData);

        // Assert: Check that the response status is 201
        $response->assertStatus(201);
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role created successfully",
            "data" => null,
        ]);

        // Assert: Verify that the role was created in the database
        $this->assertDatabaseHas('roles', [
            'role_name' =>  $roleData['role_name'],
            'description' =>  $roleData['description'],
        ]);
    }




    /** @test */
    public function it_returns_validation_name_errors_name_not_existe_when_storing_a_new_role()
    {
        // Act: Make a POST request with missing role_name
        $response = $this->actingAs($this->adminUser)->post('/api/role', [
            'description' => 'No role name provided',
        ]);

        // Assert: Check that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422);
        // Assert: Check that validation errors contain the 'role_name' field
        $response->assertJsonValidationErrors(['role_name']);
    }

    /** @test */

    public function it_returns_validation_name_errors_when_storing_a_new_role()
    {
        // Arrange: Prepare the data for a new role
        $roleData =[
        'role_name'=>'admin',
         'description'=>'very impotent description'
        ];
        $response = $this->actingAs($this->adminUser)->post('/api/role', $roleData);
        $response->assertStatus(422);
        // Assert: Check that validation errors contain the 'role_name' field
        $response->assertJsonValidationErrors(['role_name']);

    }

    /** @test */
    public function it_can_update_role()
    {
        // Arrange: Create a role to update
        $role = Role::factory()->create();

        // New data for the role update
        $role2 = Role::factory()->make()->toArray();

        // Act: Make a PUT request to update the existing role
        $response = $this->actingAs($this->adminUser)->put('/api/role/' . $role->id, $role2);

        // Assert: Check that the response status is 200
        $response->assertStatus(200);
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role updated successfully",
            "data" => null,
        ]);

        // Assert: Verify that the role was updated in the database
        $this->assertDatabaseHas('roles', [

            'role_name' => $role2['role_name'], // Check that the role name is updated
            'description' => $role2['description'], // Check that the description is updated
        ]);
    }


    /** @test */
    public function it_can_delete_role()
    {
        // Arrange: Create a role that will be deleted
        $role = Role::factory()->create();
        // Act: Make a DELETE request to remove the role
        $response = $this->actingAs($this->adminUser)->delete('/api/role/' . $role->id);
        // Assert: Check that the response status is 204
        $response->assertStatus(200);
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role deleted successfully", // Corrected message for clarity
            "data" => null,
        ]);
        // Assert: Verify that the role has been removed from the database
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id, // Verify that the role ID no longer exists
        ]);
    }


    /** @test */
    public function it_returns_error_when_non_existent_role()
    {
        // Act: Attempt to delete a role that does not exist
        $response = $this->actingAs($this->adminUser)->delete('/api/role/99'); // Assuming 99 is an invalid role ID

        // Assert: Check that the response status is 500 (Internal Server Error)
        $response->assertStatus(500);
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role not found: ", // Ensure the error message is accurate

        ]);
    }

    /** @test */
    public function it_can_show_role()
    {
        // Arrange: Create a role that will be retrieved
        $role = Role::factory()->create();

        // Act: Make a GET request to retrieve the role
        $response = $this->actingAs($this->adminUser)->get('/api/role/' . $role->id);

        // Assert: Check that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role data", // Message indicating successful retrieval
            "data" => [
                'id' => $role->id, // Ensure the ID matches
                'role_name' => $role->role_name, // Ensure the role name matches
                'description' => $role->description, // Ensure the description matches
            ],
        ]);
    }

    /** @test */

    public function it_returns_error_showing_role_not_found()
    {

        // Act: Make a GET request to retrieve the role
        $response = $this->actingAs($this->adminUser)->get('/api/role/333');
        // Assert: Check the JSON response structure
        $response->assertJson([
            "message" => "Role not found",
        ]);
    }


}
