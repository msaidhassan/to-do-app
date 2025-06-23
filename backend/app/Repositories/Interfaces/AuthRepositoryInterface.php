<?php

namespace App\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    public function createUser(array $data);
    public function attemptLogin(array $credentials): bool;
    public function createToken($user, string $name = 'auth-token');
    public function getAuthenticatedUser();
    public function logout(): void;
    public function deleteUserTokens($user): void;
}
