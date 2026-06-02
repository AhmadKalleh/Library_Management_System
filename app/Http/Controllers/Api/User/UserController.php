<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\FormRequestUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    use ResponseHelper;

    public function __construct(
        protected UserService $_userService
    ) {}

    public function index(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('viewAny', User::class);

            $raw  = $this->_userService->get_all_users($request->validated());
            $data = [
                'users'      => UserResource::collection($raw['data']['users']),
                'pagination' => $raw['data']['pagination'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function show_user(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $raw    = $this->_userService->show_user($request->validated()['user_id']);
            $target = $raw['data']['user'];

            $this->authorize('view', $target);

            $data = ['user' => new UserResource($target)];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function create_user(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('create', User::class);

            $raw  = $this->_userService->create_user($request->validated());
            $data = ['user' => new UserResource($raw['data']['user'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function update_user(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $raw    = $this->_userService->show_user($request->validated()['user_id']);
            $target = $raw['data']['user'];

            $this->authorize('update', $target);

            $raw  = $this->_userService->update_user($request->validated());
            $data = ['user' => new UserResource($raw['data']['user'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function delete_user(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $raw    = $this->_userService->show_user($request->validated()['user_id']);
            $target = $raw['data']['user'];

            $this->authorize('delete', $target);

            $raw = $this->_userService->delete_user($request->validated()['user_id'], auth()->user());

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function dashboard_stats(): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('viewAny', User::class);

            $raw  = $this->_userService->dashboard_stats();
            $data = ['stats' => $raw['data']['stats']];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }
}