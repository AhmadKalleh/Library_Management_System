<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Admin يشوف كل المستخدمين
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Admin يشوف مستخدم واحد
    public function view(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Admin ينشئ مستخدم
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Admin يعدل أي مستخدم / User يعدل نفسه فقط
    public function update(User $user, User $target): bool
    {
        return $user->role === 'admin' || $user->id === $target->id;
    }

    // Admin فقط يحذف
    public function delete(User $user, User $target): bool
    {
        return $user->role === 'admin' && $user->id !== $target->id;
    }

    // Admin فقط يحظر/يفعّل
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    // كل مستخدم يشوف بروفايله
    public function view_profile(User $user): bool
    {
        return true;
    }
}
