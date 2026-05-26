<?php

namespace App\Policies;

use App\Models\User;

class BorrowPolicy
{
    /**
     * Create a new policy instance.
     */

    public function borrow_request(User $user): bool
    {
        return $user->role === 'user';
    }

    // Admin فقط يؤكد ويرجع
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function viewAny(): bool
    {
        return true;
    }
}
