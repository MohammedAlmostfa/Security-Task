<?php

namespace App\Service;

use PDF;
use App\Models\Task;
use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Models\TaskDependencies;
use App\Models\TaskStatusUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Get all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTasks($filters)
    {
        try {
            // استرجاع الفلاتر المخزنة من الكاش
            $filtersdata = Cache::get('filters');

            // التحقق مما إذا كانت الفلاتر الجديدة مختلفة عن المخزنة
            if ($filters !== $filtersdata) {
                // إذا كانت الفلاتر مختلفة، قم بتحديث الفلاتر المخزنة في الكاش
                Cache::forget('filters');
                Cache::put('filters', $filters, 60);
                $filtersdata = $filters;
                // نسيان الكاش الخاص بالمهام لأن الفلاتر تغيرت
                Cache::forget('tasks');
            }

            // استرجاع المهام مع استخدام الفلاتر المخزنة في الكاش لمدة 60 دقيقة
            $tasks = Cache::remember('tasks', 60, function () use ($filtersdata) {
                $query = Task::query();
                return $query->type($filtersdata['type'] ?? null)
                    ->status($filtersdata['status'] ?? null)
                    ->assignedTo($filtersdata['assigned_to'] ?? null)
                    ->dueDate($filtersdata['due_date'] ?? null)
                    ->priority($filtersdata['priority'] ?? null)
                    ->dependsOn($filtersdata['depends_on'] ?? null)
                    ->paginate(5);
            });

            return $tasks;
        } catch (\Exception $e) {
            // تسجيل الخطأ وإلقاء استثناء إذا فشلت عملية استرجاع المهام
            Log::error('Failed to retrieve tasks: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }



    /**
     * Create a new task.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createTask($data)
    {
        try {
            // Create a new task using the provided data
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'status' => $data['status'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
            ]);
            return true;
        } catch (\Exception $e) {
            // Log the error and throw an exception
            Log::error('Error creating task: ' . $e->getMessage());
            throw new \Exception('Error creating task: ');
        }
    }

    /**
     * Update an existing task.
     *
     * @param int $id
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function updateTask($id, $data)
    {
        try {
            // Find the task by its ID
            $task = Task::findOrFail($id);

            // Update the task with the provided data
            $task->update([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'type' => $data['type'] ?? $task->type,
                'status' => $data['status'] ?? $task->status,
                'priority' => $data['priority'] ?? $task->priority,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
            ]);
        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the task is not found
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ');
        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error updating task: ' . $e->getMessage());
            throw new \Exception('Error updating task: ');
        }
    }

    /**
     * Show a specific task.
     *
     * @param int $id
     * @return Task
     * @throws \Exception
     */
    public function showTask($id)
    {
        try {
            // Find the task by its ID
            $task = Task::findOrFail($id);
            return $task;
        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the task is not found
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ');
        }
    }

    /**
     * Delete a task.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function destroyTask($id)
    {
        try {
            // Find the task by its ID
            $task = Task::findOrFail($id);
            $task->delete();

            return true;
        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the task is not found
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ');
        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error deleting task: ' . $e->getMessage());
            throw new \Exception('Error deleting task: ');
        }
    }



    /**
 * Update the status of a task.
 *
 * @param Task $task
 * @param string $status
 * @return bool
 * @throws \Exception
 */
    public function updateStatus(Task $task, $status)
    {
        try {
            // Check for dependencies
            $dependency = TaskDependencies::where('task_id', $task->id)->first();
            if ($dependency) {
                $dependentTask = Task::find($dependency->task_depend_on);
                if ($dependentTask->status != 'Completed') {
                    return 0;
                }
            }

            // Ensure the new status is valid and follows the correct order
            $allowedStatuses = ['Open', 'In Progress', 'Completed'];
            $currentStatusIndex = array_search($task->status, $allowedStatuses);
            $newStatusIndex = array_search($status, $allowedStatuses);

            if ($newStatusIndex === false || $newStatusIndex !== $currentStatusIndex + 1) {
                return 3;
            }

            // Update the task status
            $task->status = $status;
            $task->save();
            // Create a record of the task status update
            TaskStatusUpdate::create([
                'task_id' => $task->id,
                'task_status' => $status,
            ]);

            return 1;
        } catch (\Exception $e) {
            Log::error('Status update failed: ' . $e->getMessage());
            throw new \Exception('Status update failed: ' . $e->getMessage());
        }
    }
    /**
       * assign the status to user .
       *
       * @param  $data
       * @param integer $id
       * @return bool
       * @throws \Exception
       */

    public function assignTask($data, $id)
    {
        try {
            $task = Task::findOrFail($id);
            if ($task->assigned_to !== null) {
                return false; // Task already assigned
            } else {
                $task->assigned_to = $data['user_id'];
                $task->save();
            }
            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ');
        } catch (\Exception $e) {
            Log::error('Error assigning task: ' . $e->getMessage());
            throw new \Exception('Error assigning task: ');
        }
    }
    /**
       * reassign the status to user .
       *
       * @param integer $id
       * @return bool
       * @throws \Exception
       */

    public function reassignTask($id)
    {
        try {
            $task = Task::findOrFail($id);
            if ($task->assigned_to === null) {
                return false; // Task was not assigned to anyone
            } else {
                $task->assigned_to = null;
                $task->save();
            }
            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error reassigning task: ' . $e->getMessage());
            throw new \Exception('Error reassigning task: ');
        }
    }
    /**
       * connect task in another task .
       *
       * @param  $data
       * @return bool
       * @throws \Exception
       */

    public function connectTask($data)
    {
        try {
            TaskDependencies::create([
                'task_id' => $data['task_id'],
                'task_depend_on' => $data['depend_on_task_id'],
            ]);
            $task = Task::find($data['task_id']);

            if ($task->status != 'completed') {
                $task->status = 'Blocked';
                $task->save();
            }
        } catch (\Exception $e) {
            Log::error('Failed to create task dependency: ' . $e->getMessage());
            throw new \Exception('Failed to create task dependency: ');
        }
    }

    /**
     * Generate Daily Report.
     *
     * @return bool
     * @throws \Exception
     */
    public function generateDailyReport()
    {
        try {
            // Get data
            $tasks = TaskStatusUpdate::query()->get();
            $data = [
                'title' => 'تقريرك اليومي عن المهام',
                'date' => now()->format('Y/m/d'),
                'tasks' => $tasks
            ];

            // Get admin emails
            $users = User::where('role_id', 1)->pluck('email');
            if ($users->isEmpty()) {
                throw new \Exception('لم يتم العثور على المستخدمين لإرسال التقرير.');
            }

            $pdf = PDF::loadView('ReportPage', $data);
            $pdfPath = storage_path('app/daily-tasks-report.pdf');
            $pdf->save($pdfPath);

            SendEmailJob::dispatch($users, $pdfPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to generate daily report: ' . $e->getMessage());
            throw new \Exception('Failed to generate daily report: ');
        }
    }


    /**
     * Show tasks of the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function showTaskOfUser()
    {
        try {
            $tasks = Auth::user()->tasks;
            return $tasks;
        } catch (\Exception $e) {
            Log::error('Failed to show tasks of the user: ' . $e->getMessage());
            throw new \Exception('Failed to show tasks of the user');
        }

    }
    /**
       * show blocked tasks and latest
       *
       * @return \Illuminate\Database\Eloquent\Collection
       * @throws \Exception
       */

    public function Blockedtask()
    {
        try {
            $tasks = Task::whereHas('dependencies', function ($query) {
                $query->where('status', '!=', 'Completed')
                      ->where('due_date', '<=', now());
            })->get();
            return $tasks;
        } catch (\Exception $e) {
            Log::error('Failed to show blocked tasks: ' . $e->getMessage());
            throw new \Exception('Failed to show blocked tasks');
        }

    }
}
