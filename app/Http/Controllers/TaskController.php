<?php

namespace App\Http\Controllers;

use App\Service\TaskService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Service\ApiResponseService;
use App\Http\Requests\TaskFormRequestCreat;
use App\Http\Requests\TaskFormRequestUpdate;
use App\Http\Requests\assiganTaskformrequest;
use App\Http\Requests\connectTaskformrequest;
use App\Http\Requests\statusFormRequestUpdate;

class TaskController extends Controller
{
    protected $apiResponseService;
    protected $taskService;

    /**
     * Constructor to initialize services.
     *
     * @param TaskService $taskService
     * @param ApiResponseService $apiResponseService
     */
    public function __construct(ApiResponseService $apiResponseService, TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->apiResponseService = $apiResponseService;
    }

    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = [
            'type'        => $request->query('type'),
            'status'      => $request->query('status'),
            'assigned_to' => $request->query('assigned_to'),
            'due_date'    => $request->query('due_date'),
            'priority'    => $request->query('priority'),
            'depends_on'  => $request->query('depends_on'),
        ];

        // Retrieve all tasks and return them in the response
        $tasks = $this->taskService->getAllTasks($filters);
        return $this->apiResponseService->Showdata('All tasks', $tasks);
    }

    /**
     * Store a newly created task in storage.
     *
     * @param TaskFormRequestCreat $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskFormRequestCreat $request)
    {
        // Validate the request data
        $validatedData = $request->validated();
        // Create the task using the TaskService
        $this->taskService->createTask($validatedData);
        // Return a success response
        return $this->apiResponseService->success('Task created successfully');
    }

    /**
     * Display the specified task.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Retrieve the task by its ID and return its data in the response
        $task = $this->taskService->showTask($id);
        return $this->apiResponseService->Showdata('Task data', $task);
    }

    /**
     * Update the specified task in storage.
     *
     * @param TaskFormRequestUpdate $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskFormRequestUpdate $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validated();
        // Update the task using the TaskService
        $this->taskService->updateTask($id, $validatedData);
        // Return a success response
        return $this->apiResponseService->success('Task updated successfully');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Delete the task using the TaskService
        $this->taskService->destroyTask($id);
        // Return a success response
        return $this->apiResponseService->success('Task deleted successfully', $data = null, $status=204);
    }

    /**
     * Assign a task to a user.
     *
     * @param assiganTaskformrequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTask(assiganTaskformrequest $request, $id)
    {
        $validatedData = $request->validated();

        $result = $this->taskService->assignTask($validatedData, $id);
        if ($result === false) {
            return response()->json(['message' => 'Task is already assigned to a user'], 400);
        } else {
            return $this->apiResponseService->success('Task assigned successfully');
        }


    }

    /**
     * Reassign a task.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reassiganTask($id)
    {
        $result = $this->taskService->reassignTask($id);
        if ($result === false) {
            return response()->json(['message' => 'Task is not assigned to any user before'], 400);
        } else {
            return $this->apiResponseService->success('Task reassigned successfully');
        }
    }

    /**
     * Update the status of a task.
     *
     * @param StatusFormRequestUpdate $request
     * @param int $taskid
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateStatus(StatusFormRequestUpdate $request, $taskid)
    {
        // Retrieve the task from the request (set by Middleware)
        $task = $request->get('task');
        // Retrieve the status from the request
        $status = $request->input('status');
        // Use the service to update the status
        $result=$this->taskService->updateStatus($task, $status);
        if ($result==1) {
            return $this->apiResponseService->success('Status updated successfully');
        } elseif($result==0) {
            return $this->apiResponseService->error('Cannot update status because dependent task(s) are not completed.');
        } elseif($result==3) {
            return $this->apiResponseService->error('Cannot update  to this status');
        }
    }



    /**
     * Connect two tasks.
     *
     * @param connectTaskFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function connectTask(connectTaskFormRequest $request)
    {
        $validatedData = $request->validated();
        $this->taskService->connectTask($validatedData);
        return $this->apiResponseService->success('Task connected successfully');
    }

    /**
     * send daily report to admin .
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function generateDailyReport()
    {
        $this->taskService->generateDailyReport();
        return $this->apiResponseService->success('Report generated and emailed successfully');
    }
    /**
         * Show tasks of the authenticated user.
         *
         * @return \Illuminate\Http\JsonResponse
         */
    public function showTaskOfUser()
    {

        $tasks = $this->taskService->showTaskOfUser();
        return $this->apiResponseService->Showdata('Tasks assigned to you', $tasks);

    }

    /**
    *  show blocked tasks and latest
    *
    * @return \Illuminate\Http\JsonResponse
    */

    public function showBlockedtask()
    {
        $tasks= $this->taskService->Blockedtask();
        return $this->apiResponseService->Showdata('Blocked Task', $tasks);

    }
}
