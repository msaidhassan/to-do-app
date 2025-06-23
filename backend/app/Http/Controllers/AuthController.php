<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request)
    {

        $credentials = $request->only(['name','email', 'password']);

        $auth = $this->authService->register($credentials);



        return ApiResponse::success([
            'user' => $auth['user'],
            'token' =>  $auth['token']
        ], 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {


        $credentials = $request->only(['email', 'password']);

        $auth = $this->authService->login($credentials);

        return ApiResponse::success([
            'user' => $auth['user'],
            'token' => $auth['token']
        ], 'Login successful');
    }

    public function logout()
    {
        $this->authService->logout();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
