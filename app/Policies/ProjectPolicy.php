<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function update(User $user, Project $project): bool
    {
        return $project->exists && $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->exists && $user->hasAnyRole(['admin', 'editor']);
    }
}
