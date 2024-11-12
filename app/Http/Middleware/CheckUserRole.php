<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CheckUserRole
{
    public function handle($request, Closure $next)
    {
        try {
            $task = Task::findOrFail($request->route('taskid'));

            if ($task->assigned_to != Auth::user()->id && Auth::user()->role_id != 1) {
                return response()->json(['message' => 'This task is not assigned to you'], 403);
            }

            // Pass the task to the request
            $request->attributes->set('task', $task);

            return $next($request);
        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the task is not found
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ');
        }

    }
}
