<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('admin') && $target->exists;
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->hasRole('admin') || ! $target->exists || $user->is($target)) {
            return false;
        }

        if ($target->hasRole('admin')) {
            return User::role('admin')->whereKeyNot($target->getKey())->exists();
        }

        return true;
    }
}
