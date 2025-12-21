<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\User\CreateUserRequest as UserCreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
 

    public function getUser($id){
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }

    public function updateUser($id, UpdateUserRequest $request){
        $user = $this->userService->updateUser($id, $request);
        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }
}
