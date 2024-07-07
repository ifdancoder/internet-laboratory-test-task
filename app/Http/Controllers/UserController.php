<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsersCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AuthRequest;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
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

    public function deleteUser(Request $request, int $id) {
        $deleted = $this->userService->deleteUser($id);
        
        if ($deleted) {
            return new JsonResponse(['success' => true], 201);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function getUser(Request $request, int $id)
    {
        $user = $this->userService->getUser($id);

        if ($user) {
            return new JsonResponse(['success' => true, 'data' => (new UserResource($user))->toArray($request)], 200);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function getUsers(Request $request)
    {
        $users = $this->userService->getUserList();

        if ($users) {
            return new JsonResponse(['success' => true, 'data' => (new UserCollection($users))->toArray($request)], 200);
        }

        return new JsonResponse(['success' => false], 400);
    }

    public function updateUser(UserRequest $request, int $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());

        if ($user) {
            return new JsonResponse(['success' => true, 'data' => (new UserResource($user))->toArray($request)], 201);
        }

        return new JsonResponse(['success' => false], 400);
    }
}
