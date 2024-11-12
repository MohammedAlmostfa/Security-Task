<?php

namespace App\Service;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentService
{
    public function showCommentOfTask($taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            $comments = $task->comments;
            return $comments;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found');
        }
    }





    /**
     * Add a comment to a task.
     *
     * @param Task $task
     * @param string $body
     * @return bool
     * @throws \Exception
     */
    public function addComment(Task $task, $body)
    {
        try {
            // إنشاء تعليق جديد
            $comment = new Comment();
            $comment->body = $body;

            // حفظ التعليق إلى المهمة
            $task->comments()->save($comment);

            return $comment;

        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage());
            throw new \Exception('Error adding comment');
        }
    }




    /**
     * Update a comment on a task.
     *
     * @param Task $task
     * @param string $newbody
     * @param int $commentid
     * @return bool
     * @throws \Exception
     */
    public function UpdateComment($newbody, $commentid)
    {
        try {
            // Find the comment by its ID
            $comment = Comment::findOrFail($commentid);
            $comment->body = $newbody;

            // Save the updated comment
            $comment->save();

            return true;

        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the comment is not found
            Log::error('Comment not found: ' . $e->getMessage());
            throw new \Exception('Comment not found');

        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error updating comment: ' . $e->getMessage());
            throw new \Exception('Error updating comment');
        }
    }
    /**
        * Delete a comment from a task.
        *
        * @param int $commentid
        * @return bool
        * @throws \Exception
        */
    public function deleteComment($commentid)
    {
        try {
            // Find the comment by its ID
            $comment = Comment::findOrFail($commentid);

            // Delete the comment
            $comment->delete();

            return true;

        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the comment is not found
            Log::error('Comment not found: ' . $e->getMessage());
            throw new \Exception('Comment not found: ');

        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error deleting comment: ' . $e->getMessage());
            throw new \Exception('Error deleting comment');
        }
    }

    /**
     * Restore a deleted comment on a task.
     *
     * @param int $commentid
     * @return bool
     * @throws \Exception
     */
    public function returnComment($commentid)
    {
        try {
            // Find the deleted comment by its ID
            $comment = Comment::withTrashed()->findOrFail($commentid);

            // Restore the deleted comment
            $comment->restore();

            return true;

        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the comment is not found
            Log::error('Comment not found: ' . $e->getMessage());
            throw new \Exception('Comment not found');

        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error restoring comment: ' . $e->getMessage());
            throw new \Exception('Error restoring comment');
        }
    }
}
