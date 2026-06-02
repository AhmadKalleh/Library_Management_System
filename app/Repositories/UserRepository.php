<?php

namespace App\Repositories;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface {

public function getAll(array $filters = []): LengthAwarePaginator
{
    return User::query()
        ->when($filters['role']   ?? null, fn($q, $role)   => $q->where('role', $role))
        ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
        ->when(
            isset($filters['search']),
            fn($q) => $q->where(function ($q) use ($filters) {
                $q->where('name',  'LIKE', "%{$filters['search']}%")
                  ->orWhere('email', 'LIKE', "%{$filters['search']}%");
            })
        )
        ->paginate(15);
}

public function findById(int $id): User {
    return User::findOrFail($id);
    
}

public function create(array $data): User {
    return  User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'role' => $data['role'] ?? 'user',
    ]);
}

public function update(int $id, array $data): User {
    $user = User::findOrFail($id);
    if (isset($data['password'])){
        $data['password'] = bcrypt($data['password']);
    }   
    $user->update($data);
    return $user->fresh();
}

public function delete(int $id): bool {
    $user = User::findOrFail($id);
    return $user->delete();
}

public function getDashboardStats(): array
{
    return [
        'users' => [
            'total'    => User::count(),
            'active'   => User::where('status', 'active')->count(),
            // 'admins'   => User::where('role', 'admin')->count(),
        ],
        'books' => [
            'total'     => Book::count(),
            'available' => Book::where('available_copies', '>', 0)->count(),
        ],
        'borrows' => [
            'pending'  => Borrow::where('status', 'pending')->count(),
            'borrowed' => Borrow::where('status', 'borrowed')->count(),
            'overdue'  => Borrow::where('status', 'overdue')->count(),
        ],
    ];
}
}