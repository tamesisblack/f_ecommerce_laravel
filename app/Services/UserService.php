<?php
namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService{
    public function createUser(array $data):User{
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'lastname' => $data['lastname'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    //'image' => $data['image'] ?? null,
                ]);
                $clienteRole = Role::find('CLIENT');
                if (!$clienteRole) {
                    throw new \Exception('Role CLIENT not found');
                }
                $user->roles()->attach($clienteRole->id);
                return $user;
        });
    }

    public function login(array $data){
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpException(401, 'El usuario no existe o la contraseña es incorrecta');
        }

        $token = \JWTAuth::fromUser($user);
        return [
            'token' => 'Bearer ' . $token,
            'user' => $user
        ];
    }
}
?>