<?php
namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Translation\t;

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
}
?>