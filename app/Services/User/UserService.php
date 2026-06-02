<?php
    namespace App\Services\User;
    use App\Repositories\Interfaces\UserRepositoryInterface;
class UserService {
 
public function __construct(
    protected UserRepositoryInterface $_userRepository
) {}

public function get_all_users(): array
{
    $result = $this->_userRepository->index();

    return [
        'data'    => $result,
        'message' => 'Users retrieved successfully',
        'code'    => 200,
    ];
}

public function create_user(array $data): array
{
    $result = $this->_userRepository->create_user($data);

    return [
        'data'    => $result,
        'message' => 'User created successfully',
        'code'    => 201,
    ];
}

public function update_user(array $data): array
{
    $result = $this->_userRepository->update_user(
        $data,
        $data['user_id']
    );

    return [
        'data'    => $result,
        'message' => 'User updated successfully',
        'code'    => 200,
    ];
}

public function delete_user(array $data): array
{
    $result = $this->_userRepository->delete_user($data['user_id']);

    return [
        'data'    => $result,
        'message' => 'User deleted successfully',
        'code'    => 200,
    ];
    }

}