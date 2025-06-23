<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        $user = $this->authRepository->createUser($data);
        $token = $this->authRepository->createToken($user);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(array $credentials)
    {
        if (!$this->authRepository->attemptLogin($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $user = $this->authRepository->getAuthenticatedUser();
        $token = $this->authRepository->createToken($user);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(): void
    {
        $user = $this->authRepository->getAuthenticatedUser();
        $this->authRepository->deleteUserTokens($user);
        $this->authRepository->logout();
    }
}
