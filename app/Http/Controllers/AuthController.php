<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(\App\Services\UserService $userService){
        $this->userService = $userService;
    }

    public function login(LoginRequest $request) :JsonResponse{
        $response = $this->userService->login($request->validated());
        return response()->json($response, 200);
    }
    
    public function register(CreateUserRequest $request) :JsonResponse{
        $response = $this->userService->createUser($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $response
        ], 201);
    }
}
