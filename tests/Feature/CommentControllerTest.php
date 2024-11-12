<?php
namespace Tests\Feature;

use App\Models\Task;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
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
    /** @test */
    public function it_can_add_a_comment_to_task()
    {

        $task = Task::factory()->create();
        $user = User::factory()->create(['role_id' =>2]);
        $data = ['user_id' => $user->id];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);
        $body = "it is good";
        $response = $this->actingAs($user)->postJson('/api/tasks/' . $task->id . '/comments', ['body' => $body]);
        $response->assertJson([
            "message" => 'Comment added successfully',
            "data" => [
                "body" => $body,
                "commentable_id" => $task->id,
                "commentable_type" => Task::class,
            ]
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $body,
            'commentable_id' => $task->id,
            'commentable_type' => Task::class,
        ]);
    }


    /** @test */
    public function it_can_update_comment()
    {
        // إنشاء مهمة
        $task = Task::factory()->create();

        // تعيين مستخدم للمهمة (هذا جزء من الكود قد يتطلب إعدادات أخرى حسب الحاجة)
        $data = ['user_id' => $this->User->id];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);
        $body = "This is a bad comment";
        $commentResponse = $this->actingAs($this->User)->postJson('/api/tasks/' . $task->id . '/comments', ['body' => $body]);
        $commentId = $commentResponse->json('data')['id'];

        $updatedBody = "This comment is updated";
        $response = $this->actingAs($this->User)->putJson('/api/tasks/' . $task->id . '/comments/' . $commentId, ['body' => $updatedBody]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Comment updated successfully',
        ]);


        $this->assertDatabaseHas('comments', [
            'id' => $commentId,
            'body' => $updatedBody,
            'commentable_id' => $task->id, // تأكد من أن التعليق مرتبط بالمهمة
            'commentable_type' => Task::class, // تأكد من أن العلاقة polymorphic هي Task
        ]);
    }

    /** @test */

    public function it_can_soft_delete_comment()
    {
        $task = Task::factory()->create();


        $data = ['user_id' => $this->User->id];
        $this->actingAs($this->adminUser)
             ->post('/api/tasks/' . $task->id . '/assign', $data);
        $body = "This is a bad comment";
        $commentResponse = $this->actingAs($this->User)->postJson('/api/tasks/' . $task->id . '/comments', ['body' => $body]);
        $commentId = $commentResponse->json('data')['id'];

        $response = $this->actingAs($this->adminUser)->deleteJson('/api/tasks/' . $task->id . '/comments/' . $commentId);

        $response->assertJson([
            'message' =>'Comment deleted successfully',
        ]);
        $this->assertSoftDeleted('comments', [
            'id' => $commentId,
        ]);
    }

    /** @test */

    public function return_deleted_comments()
    {
        $task = Task::factory()->create();
        $data = ['user_id' => $this->User->id];
        $this->actingAs($this->adminUser)->post('/api/tasks/' . $task->id . '/assign', $data);
        $body = "This is a bad comment";
        $commentResponse = $this->actingAs($this->User)->postJson('/api/tasks/' . $task->id . '/comments', ['body' => $body]);
        $commentId = $commentResponse->json('data')['id'];
        $this->actingAs($this->adminUser)->deleteJson('/api/tasks/' . $task->id . '/comments/' . $commentId);
        $response = $this->actingAs($this->adminUser)->postJson('/api/tasks/' . $task->id . '/comments/' . $commentId);
        $response->assertJson([
            'message' =>'Comment restored successfully',
        ]);
        $this->assertDatabaseHas('comments', [
           'id' => $commentId,
           'body' => $body,
           'commentable_id' => $task->id, // تأكد من أن التعليق مرتبط بالمهمة
           'commentable_type' => Task::class, // تأكد من أن العلاقة polymorphic هي Task
      ]);


    }
}
