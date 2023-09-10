<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\{LoginRequest, RegisterRequest};
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\DTOs\Auth\UserDTO;
use App\Http\Resources\{UserResource, AuthResource};
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $userDTO = new UserDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );

        $user = $this->authService->register($userDTO);

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $device_name = $request->input('device_name');

        $token = $this->authService->login($email, $password, $device_name);

        if (!$token) {
            return response()->json(['message' => __('messages.login_failed')], Response::HTTP_UNAUTHORIZED);
        }

        return (new AuthResource(['access_token' => $token]))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function me()
    {
        $user = auth()->user();

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $client = $request->user();

        $client->tokens()->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
