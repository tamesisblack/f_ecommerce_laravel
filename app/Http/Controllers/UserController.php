<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\User\CreateUserRequest as UserCreateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(UserCreateUserRequest $request, UserService $userService){
        $user = $userService->createUser($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'user' => $user
        ], 201);
    }
}
