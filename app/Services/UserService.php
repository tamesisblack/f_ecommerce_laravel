<?php
namespace App\Services;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService{
    public function createUser(array $data){
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
                $token = \JWTAuth::fromUser($user);
                $user->load('roles');
                return [
                    'token' => 'Bearer ' . $token,
                    'user' => $user
                ];
        });
    }

    public function login(array $data){
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpException(401, 'El usuario no existe o la contraseña es incorrecta');
        }

        $token = \JWTAuth::fromUser($user);
        $user->load('roles');
        
        // Agregar URL completa para la imagen usando el accessor
        if ($user->image) {
            $user->image = url($user->image_url ? : $user->image);
        }
        
        return [
            'token' => 'Bearer ' . $token,
            'user' => $user
        ];
    }

    public function getUserById($id) : ?User {
        $user = User::with('roles')->find($id);
        if ($user) {
            // Usar el accessor para obtener la URL completa
            $user->image = $user->image_url;
        }
        return $user;
    }

    public function updateUser(int $id, UpdateUserRequest $request): User {
        return DB::transaction(function () use ($id, $request) {
            $user = User::with('roles')->findOrFail($id);

            if($request->filled('name')){
                $user->name = $request->input('name');
            }
            if($request->filled('lastname')){
                $user->lastname = $request->input('lastname');
            }
            if($request->filled('phone')){
                $user->phone = $request->input('phone');
            }
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior si existe
                if ($user->image) {
                    $oldImagePath = str_replace('/storage/', '', $user->image);
                    \Storage::disk('public')->delete($oldImagePath);
                }
                
                $imagePath = $request->file('image')->store("user_images/{$user->id}", 'public');
                $user->image = $imagePath; // Guardamos solo el path relativo
            }
            $user->save();
            
            // Agregamos la URL completa solo para la respuesta usando el accessor
            if ($user->image) {
                $user->image = $user->image_url;
            }
            // commit transaction
            DB::commit();
            return $user;
        });
    }
}
?>