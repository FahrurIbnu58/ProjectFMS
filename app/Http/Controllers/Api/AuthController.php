<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $auth)
    {
    }

    public function login(LoginRequest $request)
    {
        [$user, $token] = $this->auth->login($request->validated()['email'], $request->validated()['password']);

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()), 'Authenticated user.');
    }

    public function logout(Request $request)
    {
        $this->auth->logout($request->user());

        return $this->success(null, 'Logged out.');
    }
}
