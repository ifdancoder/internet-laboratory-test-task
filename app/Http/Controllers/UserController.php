<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\FindUserRequest;
use App\Http\Requests\DeleteUserRequest;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegistrationRequest $request)
    {
        $user_array = $this->userService->register($request->validated());

        if ($user_array['user']) {
            return new JsonResponse(['success' => true, 'data' => [
                'user' => (new UserResource($user_array['user']))->toArray($request),
                'token' => $user_array['token']
                ]
            ], 201);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function authenticate(AuthRequest $request)
    {
        $user_array = $this->userService->authenticate($request->validated());

        if ($user_array && $user_array['user']) {
            return new JsonResponse(['success' => true, 'data' => [
                'user' => (new UserResource($user_array['user']))->toArray($request),
                'token' => $user_array['token']
                ]
            ], 201);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function deleteUser(DeleteUserRequest $request) {
        $deleted = $this->userService->deleteUser($request->validated()['id']);

        return new JsonResponse(['success' => $deleted], 201);
    }

    public function getUser(FindUserRequest $request)
    {
        $user = $this->userService->getUser($request->validated()['id']);

        if ($user) {
            return new JsonResponse(['success' => true, 'data' => (new UserResource($user))->toArray($request)], 200);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function updateUser(UserRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        unset($validated['id']);
        $user = $this->userService->updateUser($id, $validated);

        if ($user) {
            return new JsonResponse(['success' => true, 'data' => (new UserResource($user))->toArray($request)], 201);
        }

        return new JsonResponse(['success' => false], 400);
    }
}
