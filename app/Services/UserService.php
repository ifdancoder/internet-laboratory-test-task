<?php

namespace App\Services;

use App\Models\User;
use \Auth;

class UserService {
    public function register(array $data)
    {
        $user = User::create($data);
        $token = $user->createToken('MyApp')->plainTextToken;
        
        $user->token = $token;

        return ['user' => $user, 'token' => $token];
    }

    public function authenticate(array $data)
    {
        if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;

            $user->token = $token;

            return ['user' => $user, 'token' => $token];
        }

        return null;
    }

    public function deleteUser(int $id)
    {
        $user = User::find($id);
        $deleted = $user->delete();

        return $deleted;
    }

    public function getUser(int $id)
    {
        $user = User::find($id);

        return $user;
    }

    public function updateUser(int $id, array $data)
    {
        $user = User::find($id);
        $user->update($data);
        $user->save();

        return $user;
    }
}