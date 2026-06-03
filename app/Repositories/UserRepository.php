<?php

namespace App\Repositories;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\Upload\UplodeImageHelper;

class UserRepository implements UserRepositoryInterface
{
    use UplodeImageHelper;

    private function base_query(array $filters = [])
    {
        $query = User::with(['image', 'borrows'])
            ->where('role', 'user');

        if($filters['status'] != 'all') {
            $query->where('status', $filters['status'] ?? 'active');
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('mobile', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query;
    }


    public function index(array $data = []): array
    {
        $users = $this->base_query($data)
            ->latest()
            ->paginate(10);

        return [
            'users'      => $users->items(),
            'pagination' => [
                'current_page'  => $users->currentPage(),
                'last_page'     => $users->lastPage(),
                'per_page'      => $users->perPage(),
                'total'         => $users->total(),
                'has_more'      => $users->hasMorePages(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ],
        ];
    }


    public function show_user(int $user_id): array
    {
        $user = User::with(['image', 'borrows.book'])
            ->findOrFail($user_id);

        $has_overdue = $user->borrows()
            ->where('status', 'overdue')
            ->exists();

        return [
            'user'        => $user,
            'has_overdue' => $has_overdue,
        ];
    }


    public function create(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'mobile'   => $data['mobile'],
            'password' => Hash::make($data['password']),
            'role'     => strtolower($data['role'] ?? 'user'),
            'email_verified_at' => now(),
            'status'   => 'active',
        ]);


        if (!empty($data['image'])) {
            $user->image()->create(['path' => $this->uplodeImage($data['image'],'users')]);
        }

        return ['user' => $user->load('image')];
    }


    public function update(int $user_id, array $data): array
    {
        $user = User::findOrFail($user_id);

        $user->update([
            'name'   => $data['name']   ?? $user->name,
            'email'  => $data['email']  ?? $user->email,
            'mobile' => $data['mobile'] ?? $user->mobile,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['image'])) {

            $newHash = md5_file($data['image']->getRealPath());

            $oldHash = $user->image ? md5(Storage::disk('public')->get($user->image->path)) : null;

            if ($newHash != $oldHash) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image->path);
                    $user->image()->delete();
                }

                $path = $this->uplodeImage($data['image'], 'users');
                $user->image()->create(['path' => $path]);
            }

        }

        return ['user' => $user->load('image')];
    }


    public function delete(int $user_id): array
    {
        $user = User::findOrFail($user_id);

        $has_active_borrows = $user->borrows()
            ->whereIn('status', ['pending', 'borrowed'])
            ->exists();

        if ($has_active_borrows) {
            return ['status' => 'has_active_borrows'];
        }

        if ($user->image && Storage::disk('public')->exists($user->image->path))
        {
            Storage::disk('public')->delete($user->image->path);
            $user->image()->delete();
        }

        $user->delete();

        return ['status' => 'deleted'];
    }


    public function getDashboardStats(): array
    {
        return [
            'total_books'       => Book::count(),
            'total_users'       => User::where('role', 'user')->count(),
            'borrowed_books'    => Borrow::whereIn('status', ['borrowed', 'overdue'])->count(),
            'available_books'   => Book::where('status', 'available')->count(),
            'pending_requests'  => Borrow::where('status', 'pending')->count(),
            'overdue_borrows'   => Borrow::where('status', 'overdue')->count(),
            'banned_users'      => User::where('role', 'user')->where('status', 'banned')->count(),
        ];
    }


    public function show_profile(): array
    {
        $user = Auth::user()->load(['image']);

        return [
            'user'           => $user,
        ];
    }


    public function active_user(int $user_id): array
    {
        $user = User::findOrFail($user_id);

        if ($user->status === 'active') {
            return ['status' => 'already_active'];
        }

        $user->update(['status' => 'active']);

        return ['status' => 'activated', 'user' => $user];
    }


    public function inactive_user(int $user_id): array
    {
        $user = User::findOrFail($user_id);

        if ($user->status === 'banned') {
            return ['status' => 'already_banned'];
        }

        $user->update(['status' => 'banned']);


        $user->borrows()
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return ['status' => 'banned', 'user' => $user];
    }
}
