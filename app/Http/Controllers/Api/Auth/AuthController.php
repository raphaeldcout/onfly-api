<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\{LoginRequest, RegisterRequest};
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\DTOs\Auth\UserDTO;
use App\Http\Resources\{UserResource, AuthResource};
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Auth',
    description: 'Authorization API'
)]
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[OA\Post(
        path: '/register',
        summary: 'Register a new user.',
        requestBody: new OA\RequestBody(
            description: 'Input data',
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['name', 'email', 'password', 'password_confirmation'],
                        properties: [
                            new OA\Property(
                                property: 'name',
                                description: 'Name to be created',
                                type: 'string',
                                maxLength: 255,
                            ),
                            new OA\Property(
                                property: 'email',
                                description: 'Email to be created',
                                type: 'string',
                                maxLength: 255
                            ),
                            new OA\Property(
                                property: 'password',
                                description: 'Password to be created',
                                type: 'string',
                                minLength: 6
                            ),
                            new OA\Property(
                                property: 'password_confirmation',
                                description: 'Confirm Password to be compared with Password',
                                type: 'string',
                                minLength: 6
                            ),
                        ],
                        type: 'object'
                    )
                ),
            ]
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Object response `Data` content `User` property'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 422, description: 'The given data was invalid'),
        ]
    )]
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

    #[OA\Post(
        path: '/login',
        summary: 'Login a user.',
        requestBody: new OA\RequestBody(
            description: 'Input data',
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['name', 'email', 'device_name'],
                        properties: [
                            new OA\Property(
                                property: 'name',
                                description: 'Name created',
                                type: 'string',
                                maxLength: 255,
                            ),
                            new OA\Property(
                                property: 'email',
                                description: 'Email created',
                                type: 'string',
                                maxLength: 255
                            ),
                            new OA\Property(
                                property: 'device_name',
                                description: 'Device name origin request',
                                type: 'string'
                            )
                        ],
                        type: 'object'
                    )
                ),
            ]
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object response `Data` content `User` property'
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'The given data was invalid'),
        ]
    )]
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

    #[OA\Get(
        path: '/me',
        summary: 'Display the specified user.',
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object response `Data` content `User` property'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 404, description: 'Page not found'),
        ]
    )]
    public function me()
    {
        $user = auth()->user();

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/logout',
        summary: 'Logout a user.',
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'No content'
            ),
            new OA\Response(response: 400, description: 'Bad request')
        ]
    )]
    public function logout(Request $request)
    {
        $client = $request->user();

        $client->tokens()->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
