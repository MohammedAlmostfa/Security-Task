<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskDependencies;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    // Admin user and regular user for testing purposes
    protected $adminUser;
    protected $User;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user (role_id 1) and a regular user (role_id 2)
        $this->adminUser = User::factory()->create(['role_id' => 1]);
        $this->User = User::factory()->create(['role_id' => 2]); // Create a regular user
    }

    /** @test */
    public function Unauthorized_for_you()
    {
        // Test: Unauthorized user trying to access tasks
        $task = Task::all();
        $response = $this->actingAs($this->User)->get('/api/task');

        // Assert that the user is unauthorized (403 status code)
        $response->assertStatus(403);
        $response->assertJson([
            "message" => 'Unauthorized'
        ]);
    }

    /** @test */
    public function it_can_get_all_tasks()
    {
        // Test: Admin user can retrieve all tasks
        $task = Task::all(); // استرجاع جميع المهام

        // إرجاع الاستجابة من الـ API
        $response = $this->actingAs($this->adminUser)->get('/api/task');

        // Assert the response status is OK (200)
        $response->assertStatus(200);

        // Assert that the message is correct
        $response->assertJson([
            'message' => 'All tasks',
        ]);

        // Assert the pagination structure and data
        $response->assertJsonStructure([
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'created_at',
                        'updated_at',
                        'title',
                        'type',
                        'status',
                        'priority',
                        'due_date',
                        'description',
                        'assigned_to',
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);

        $response->assertJsonFragment([
            'data' => $task->toArray(),
        ]);
    }


    /** @test */
    public function it_can_store_a_new_task()
    {
        // Test: Admin user can create a new task
        $task = Task::factory()->make()->toArray(); // Make a new task but not save it yet

        // Send a POST request to create the task
        $response = $this->actingAs($this->adminUser)->post('/api/task', $task);

        // Assert successful creation response (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            "message" => "Task created successfully",
        ]);

        // Assert the task has been created in the database
        $this->assertDatabaseHas('tasks', [
            'title' => $task['title'],
            'description' => $task['description'],
        ]);
    }

    /** @test */
    public function it_can_update_task()
    {
        // Test: Admin user can update a task
        $task = Task::factory()->create(); // Create a task
        $task2 = Task::factory()->make()->toArray(); // Make new task data for updating

        // Send a PUT request to update the task
        $response = $this->actingAs($this->adminUser)->put('/api/task/' . $task->id, $task2);

        // Assert that the response is successful (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            "message" => "Task updated successfully",
        ]);

        // Assert that the task has been updated in the database
        $this->assertDatabaseHas('tasks', [
            'title' => $task2['title'],
            'description' => $task2['description'],
        ]);
    }

    /** @test */
    public function it_can_delete_task()
    {
        // Test: Admin user can delete a task
        $task = Task::factory()->create(); // Create a task

        // Send a DELETE request to remove the task
        $response = $this->actingAs($this->adminUser)->delete('/api/task/' . $task->id);

        // Assert successful deletion response (204 status)
        $response->assertStatus(204);

        // Assert that the task has been removed from the database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_can_show_task()
    {
        // Test: Admin user can view a task's details
        $task = Task::factory()->create(); // Create a task
        $response = $this->actingAs($this->adminUser)->get('/api/task/' . $task->id);

        // Assert that the task data is returned with a successful response (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task data',
            'data' => $task->toArray(),
        ]);

        // Assert the task exists in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_can_assign_task()
    {
        // Test: Admin user can assign a task to a user
        $task = Task::factory()->create();
        $user = User::factory()->create(['role_id' => 2]); // Regular user to assign the task to
        $data = ['user_id' => $user->id]; // Data to assign the task

        // Send a POST request to assign the task
        $response = $this->actingAs($this->adminUser)
                         ->post('/api/tasks/' . $task->id . '/assign', $data);

        // Assert the response is successful (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task assigned successfully',
        ]);

        // Assert the task has been assigned in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_to' => $user->id,
        ]);
    }

    /** @test */
    public function The_task_assigned_before()
    {
        // Test: Attempt to assign a task that has already been assigned to a user
        $task = Task::factory()->create();
        $user = User::factory()->create(['role_id' => 2]); // Regular user
        $data = ['user_id' => $user->id];

        // First assignment
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);

        // Attempt to reassign the same task
        $response = $this->actingAs($this->adminUser)
                         ->post('/api/tasks/' . $task->id . '/assign', $data);

        // Assert that the response returns a 400 status and a proper error message
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Task is already assigned to a user',
        ]);

        // Assert the task remains assigned in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_to' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_reassign_task()
    {
        // Test: Admin user can reassign a task to a different user
        $task = Task::factory()->create();
        $user1 = User::factory()->create(['role_id' => 2]); // First user to assign the task to

        // Assign the task to the first user
        $data1 = ['user_id' => $user1->id];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data1);

        // Reassign the task to a new user
        $response = $this->actingAs($this->adminUser)
                         ->put('/api/tasks/' . $task->id . '/reassign');

        // Assert successful reassignment response (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task reassigned successfully',
        ]);

        // Assert the task has been reassigned in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_can_not_reassign_task()
    {
        // Test: Cannot reassign a task that hasn't been assigned to anyone
        $task = Task::factory()->create();
        $user = User::factory()->create(['role_id' => 2]); // Regular user
        $data = ['user_id' => $user->id];

        // Attempt to reassign the task without assigning it first
        $response = $this->actingAs($this->adminUser)
                         ->put('/api/tasks/' . $task->id . '/reassign', $data);

        // Assert the response is a 400 status and the proper error message
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Task is not assigned to any user before',
        ]);

        // Assert the task remains unassigned in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_can_connect_task()
    {
        // Test: Admin user can connect tasks (dependencies)
        $task = Task::factory()->create();
        $task2 = Task::factory()->create();

        // Connect task2 to task as a dependency
        $data = [
            'task_id' => $task->id,
            'depend_on_task_id' => $task2->id,
        ];

        // Send POST request to connect tasks
        $response = $this->actingAs($this->adminUser)
                         ->post('/api/tasks/connect', $data);

        // Assert successful connection response (200 status)
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task connected successfully',
        ]);

        // Assert the connection is saved in the database
        $this->assertDatabaseHas('task_dependencies', [
            'task_id' => $task->id,
            'task_depend_on' => $task2->id,
        ]);

        // Assert that the task status has been updated to "Blocked"
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Blocked',
        ]);
    }

    /** @test */
    public function it_can_update_status()
    {
        // Test: User can update the status of an assigned task
        $task = Task::factory()->create(['status' => "Open"]);
        $data = ['user_id' => $this->User->id]; // Assign user to the task

        // Assign the task to the user
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);

        // Send PUT request to update the status of the task
        $response = $this->actingAs($this->User)
                         ->put('/api/tasks/' . $task->id . '/status', ['status' => 'In Progress']);

        // Assert successful status update response (200 status)
        $response->assertJson([
            'message' => 'Status updated successfully',
        ]);

        // Assert the task's status is updated to "In Progress"
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'In Progress',
        ]);
    }

    /** @test */
    public function it_can_not_update_status()
    {
        // Test: User can't update the status of a task not assigned to them
        $user = User::factory()->create(['role_id' => 2]);

        $task = Task::factory()->create(['status' => "Open"]);

        $data = ['user_id' => $user->id];

        // Assign the task to another user
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);

        // Try to update the task status as a different user (not assigned to the task)
        $response = $this->actingAs($this->User)
                         ->put('/api/tasks/' . $task->id . '/status', ['status' => 'In Progress']);

        // Assert that a 403 status is returned with the proper error message
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This task is not assigned to you',
        ]);

        // Assert that the task status remains unchanged in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Open',
        ]);
    }

    /** @test */
    public function it_can_update_status_due_to_dependency()
    {
        // Test: A task's status can be updated if its dependencies are completed
        $task1 = Task::factory()->create(['status' => "Completed"]);
        $task2 = Task::factory()->create(['status' => "Open"]);

        // Create a dependency for task2 to be blocked by task1
        TaskDependencies::create([
            'task_id' => $task2->id,
            'task_depend_on' => $task1->id,
        ]);

        $data = ['user_id' => $this->User->id];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task2->id . '/assign', $data);

        // Send a PUT request to update the status of task2
        $response = $this->actingAs($this->User)
                         ->put('/api/tasks/' . $task2->id . '/status', ['status' => 'In Progress']);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Status updated successfully',
        ]);

        // Assert the task's status has been updated to "In Progress"
        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'status' => 'In Progress',
        ]);
    }

    /** @test */
    public function it_can_not_update_status_due_to_dependency()
    {
        // Test: Task cannot be updated if its dependency is not completed
        $task = Task::factory()->create(['status' => "Open"]);
        $task2 = Task::factory()->create(['status' => "Open"]); // Task that depends on task1

        $user = User::factory()->create(['role_id' => 2]);

        $data = ['user_id' => $user->id];

        // Assign both tasks to different users
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task2->id . '/assign', ['user_id' => $this->User->id]);

        // Connect task2 to task as a dependency
        $data = [
            'task_id' => $task2->id,
            'depend_on_task_id' => $task->id,
        ];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/connect', $data);

        // Try to update task2's status to "In Progress" while task1 is not completed
        $response = $this->actingAs($this->User)
                         ->put('/api/tasks/' . $task2->id . '/status', ['status' => 'In Progress']);

        // Assert that the response indicates a blocked status because dependencies are not completed
        $response->assertJson([
            'message' => 'Cannot update status because dependent task(s) are not completed.',
        ]);

        // Assert that task2's status remains blocked
        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'status' => 'Blocked',
        ]);
    }
    /** @test */

    public function test_show_tasks_of_user()
    {
        // إنشاء مستخدم وتوثيقه
        $user = User::factory()->create(['role_id' => 2]);



        $tasks = Task::factory()->count(3)->create(['assigned_to' => $user->id]);


        $response = $this->actingAs($user)->getJson('/api/users/tasks');


        $response->assertJson([
            'message' => 'Tasks assigned to you',
            'data' => $tasks->toArray(),
        ]);
    }











}
