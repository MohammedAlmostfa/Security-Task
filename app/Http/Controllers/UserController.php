<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequestCreat;
use App\Http\Requests\UserFormRequestUpdate;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    /**
     * Constructor to inject UserService.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Show all users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = $this->userService->showUsers();

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }

    /**
     * Store a newly created user.
     *
     * @param UserFormRequestCreat $request
     * @return JsonResponse
     */
    public function store(UserFormRequestCreat $request): JsonResponse
    {
        // Get the validation data
        $validatedData = $request->validated();

        // Get the result
        $result = $this->userService->createUser($validatedData);

        // Return the result
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }

    /**
     * Update a user.
     *
     * @param UserFormRequestUpdate $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserFormRequestUpdate $request, int $id): JsonResponse
    {
        // Get the validation data
        $validatedData = $request->validated();

        // Get the result
        $result = $this->userService->updateUser($validatedData, $id);

        // Return the result
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }

    /**
     * Delete a user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // Delete the user
        $result = $this->userService->deletUser($id);
        // Return response
        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }


    /**
     * Show a user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Show the user
        $result = $this->userService->showUser($id);

        // Return the response
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
}
