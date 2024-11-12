<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Service\AuthService;
use App\Models\User;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }
    //**________________________________________________________________________________________________
    /**
     ** logain user
     ** @param LoginRequest $request(user data)
     ** @return Responsejson(data,message,authorisation)
     */

    public function login(LoginRequest $request)
    {
        // Retrieve the validated data from the request
        $credentials = $request->validated();
        // login the user
        $result = $this->authService->login2($credentials);
        //return Responsejson
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
            'authorisation' => $result['authorisation']
        ], $result['status']);
    }
    //**________________________________________________________________________________________________
    /**
     ** Log out user
     ** @param nothing
     ** @return Responsejson(data ,user's )
     */
    public function logout()
    {
        // logout the user
        $result = $this->authService->logout2();
        //return Responsejson
        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }
    //**________________________________________________________________________________________________
    /**
     ** refresh user
     ** @param Nothing
     ** @return Responsejson(data ,user's data,authorisation)
     */
    public function refresh()
    {
        //update user
        $result = $this->authService->refresh2();
        //return Responsejson
        return response()->json([
            'message' => $result['message'],
            'user' => $result['user'],
            'authorisation' => $result['authorisation'],
        ], $result['status']);
    }
}
