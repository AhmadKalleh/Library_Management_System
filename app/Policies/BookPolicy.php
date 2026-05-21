<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }


    public function update(User $user): bool
    {
        return $user->role === 'admin';
    }


    public function delete(User $user): bool
    {
        return $user->role === 'admin';
    }


    public function viewAny(): bool
    {
        return true;
    }


    public function view(): bool
    {
        return true;
    }
}
