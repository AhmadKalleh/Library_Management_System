<?php

namespace App\Services\User;

use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $_userRepository
    ) {}

    public function get_all_users(array $data = []): array
    {
        $result = $this->_userRepository->index($data);

        return [
            'data'    => $result,
            'message' => 'Users retrieved successfully',
            'code'    => 200,
        ];
    }

    public function get_user(int $user_id): array
    {
        $result = $this->_userRepository->show_user($user_id);

        return [
            'data'    => $result,
            'message' => 'User retrieved successfully',
            'code'    => 200,
        ];
    }

    public function create_user(array $data): array
    {
        $result = $this->_userRepository->create($data);

        return [
            'data'    => $result,
            'message' => 'User created successfully',
            'code'    => 201,
        ];
    }

    public function update_user(int $user_id, array $data): array
    {
        $result = $this->_userRepository->update($user_id, $data);

        return [
            'data'    => $result,
            'message' => 'User updated successfully',
            'code'    => 200,
        ];
    }

    public function delete_user(int $user_id): array
    {
        $result = $this->_userRepository->delete($user_id);

        if ($result['status'] === 'has_active_borrows') {
            return [
                'data'    => [],
                'message' => 'Cannot delete user with active borrows',
                'code'    => 409,
            ];
        }

        return [
            'data'    => [],
            'message' => 'User deleted successfully',
            'code'    => 200,
        ];
    }

    public function get_dashboard_stats(): array
    {
        $result = $this->_userRepository->getDashboardStats();

        return [
            'data'    => $result,
            'message' => 'Dashboard stats retrieved successfully',
            'code'    => 200,
        ];
    }

    public function get_profile(): array
    {
        $result = $this->_userRepository->show_profile();

        return [
            'data'    => $result,
            'message' => 'Profile retrieved successfully',
            'code'    => 200,
        ];
    }

    public function activate_user(int $user_id): array
    {
        $result = $this->_userRepository->active_user($user_id);

        return match($result['status']) {
            'already_active' => [
                'data'    => [],
                'message' => 'User is already active',
                'code'    => 409,
            ],
            'activated' => [
                'data'    => $result['user'],
                'message' => 'User activated successfully',
                'code'    => 200,
            ],
        };
    }

    public function ban_user(int $user_id): array
    {
        $result = $this->_userRepository->inactive_user($user_id);

        return match($result['status']) {
            'already_banned' => [
                'data'    => [],
                'message' => 'User is already banned',
                'code'    => 409,
            ],
            'banned' => [
                'data'    => $result['user'],
                'message' => 'User banned successfully',
                'code'    => 200,
            ],
        };
    }
}
