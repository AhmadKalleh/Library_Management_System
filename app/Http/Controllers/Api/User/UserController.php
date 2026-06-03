<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserActionRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\UserRequests\FormRequestUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

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

            $this->authorize('view', User::class);

            $raw  = $this->_userService->get_user($request->user_id);
            $data = [
                'user'        => new UserResource($raw['data']['user']),
                'has_overdue' => $raw['data']['has_overdue'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

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

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function update_user(FormRequestUser $request,int $user): JsonResponse
    {
        $data = [];

        try {
            $target = User::findOrFail($user);
            $this->authorize('update', $target);

            $raw  = $this->_userService->update_user($user, $request->validated());
            $data = ['user' => new UserResource($raw['data']['user'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function delete_user(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $target = User::findOrFail($request['user_id']);
            $this->authorize('delete', $target);

            $raw = $this->_userService->delete_user($request['user_id']);

            if ($raw['code'] !== 200) {
                return $this->Error($data, $raw['message'], $raw['code']);
            }

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function dashboard_statistics(): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('viewAny', User::class);

            $raw  = $this->_userService->get_dashboard_stats();
            $data = $raw['data'];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function profile(): JsonResponse
    {
        $data = [];

        try {
            $raw  = $this->_userService->get_profile();
            $data = [
                'user'           => new UserResource($raw['data']['user']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function activate(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('manage', User::class);

            $raw = $this->_userService->activate_user($request['user_id']);

            if ($raw['code'] !== 200) {
                return $this->Error($data, $raw['message'], $raw['code']);
            }

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function ban(FormRequestUser $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('manage', User::class);

            $raw = $this->_userService->ban_user($request['user_id']);

            if ($raw['code'] !== 200) {
                return $this->Error($data, $raw['message'], $raw['code']);
            }

            return $this->Success($data, $raw['message'], $raw['code']);

        }  catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }
}
