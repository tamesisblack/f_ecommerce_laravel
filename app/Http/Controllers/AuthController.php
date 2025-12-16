<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
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
        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => $response
        ], 200);
    }
}
