<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
   
    public function viewAny(User $auth): bool
    {
        return $auth->role === 'admin';
    }

   
    public function view(User $auth, User $target): bool
    {
        return $auth->role === 'admin' || $auth->id === $target->id;
    }


    public function create(User $auth): bool
    {
        return $auth->role === 'admin';
    }


    public function update(User $auth, User $target): bool
    {
        return $auth->role === 'admin' || $auth->id === $target->id;
    }


    public function delete(User $auth, User $target): bool
    {
        return $auth->role === 'admin' && $auth->id !== $target->id;
    }
}