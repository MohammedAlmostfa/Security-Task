<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleFormRequestcreat;
use App\Http\Requests\RoleFormRequestupdate;
use App\Service\RoleService;
use Illuminate\Http\Request;
use App\Service\ApiResponseService;

class RoleController extends Controller
{
    protected $apiResponseService;
    protected $roleService;

    /**
     * Constructor to initialize services.
     *
     * @param RoleService $roleService
     * @param ApiResponseService $apiResponseService
     */
    public function __construct(ApiResponseService $apiResponseService, RoleService $roleService)
    {
        $this->roleService = $roleService;
        $this->apiResponseService = $apiResponseService;
    }

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve all roles and return them in the response
        $roles = $this->roleService->getAllRoles();
        return $this->apiResponseService->Showdata('All roles', $roles);
    }

    /**
     * Store a newly created role in storage.
     *
     * @param RoleFormRequestcreat $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleFormRequestcreat $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Create the role using the RoleService
        $this->roleService->createRole($validatedData);

        // Return a success response
        return $this->apiResponseService->success('Role created successfully', $data=null, $status=201);
    }

    /**
     * Display the specified role.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Retrieve the Role by its ID and return its data in the response
        $task = $this->roleService->showRole($id);
        return $this->apiResponseService->Showdata('Role data', $task);
    }

    /**
     * Update the specified Role in storage.
     *
     * @param TaskFormRequestUpdate $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleFormRequestupdate $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validated();
        // Update the Role using the TaskService
        $this->roleService->updateRole($id, $validatedData);
        // Return a success response
        return $this->apiResponseService->success('Role updated successfully');
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Delete the Role using the TaskService
        $this->roleService->destroyRole($id);
        // Return a success response
        return $this->apiResponseService->success('Role deleted successfully');
    }
}
