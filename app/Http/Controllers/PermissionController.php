<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PermissionService;
use App\Service\ApiResponseService;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\PermissionRequestupdate;
use App\Http\Requests\PermissionFormRequestcreat;

class PermissionController extends Controller
{
    protected $apiResponseService;
    protected $permissionService;

    /**
     * Constructor to initialize services.
     *
     * @param ApiResponseService $apiResponseService
     * @param PermissionService $permissionService
     */
    public function __construct(ApiResponseService $apiResponseService, PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        $this->apiResponseService = $apiResponseService;
    }

    /**
     * Display a listing of the permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $permission= $this->permissionService->getAllPermissions();


        return $this->apiResponseService->success('All permission', $permission);


    }

    /**
     * Store a newly created permission in storage.
     *
     * @param PermissionFormRequestcreat $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PermissionFormRequestcreat $request)
    {
        // Validate the request data
        $validatedData = $request->validated();
        // Create the permission using the PermissionService
        $this->permissionService->createPermission($validatedData);
        // Return a success response
        return $this->apiResponseService->success('Permission created successfully', null, 201);
    }

    /**
     * Display the specified permission.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Retrieve the permission by its ID and return its data in the response
        $permission = $this->permissionService->showPermission($id);
        return $this->apiResponseService->Showdata('Permission data', $permission);
    }

    /**
     * Update the specified permission in storage.
     *
     * @param PermissionRequestupdate $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PermissionRequestupdate $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validated();
        // Update the permission using the PermissionService
        $this->permissionService->updatePermission($id, $validatedData);
        // Return a success response
        return $this->apiResponseService->success('Permission updated successfully');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Delete the permission using the PermissionService
        $this->permissionService->destroyPermission($id);
        // Return a success response
        return $this->apiResponseService->success($message ='Permission deleted successfully', $data = null, $status=204);
    }

    public function addPermissionToRole($permissionId, $roleId)
    {
        $this->permissionService->addPermissionToRole($permissionId, $roleId);
        return $this->apiResponseService->success('Permission added to role successfully');

    }
}
