<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthRepository implements AuthRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
    public function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function getAuthenticatedUser()
    {
        return Auth::user();
    }
    public function createUser(array $data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }


    public function createToken($user, string $name = 'auth-token')
    {
        return $user->createToken($name);
    }
    public function logout(): void
    {
        Auth::logout();
    }

    public function deleteUserTokens($user): void
    {
        $user->tokens()->delete();
    }
}
