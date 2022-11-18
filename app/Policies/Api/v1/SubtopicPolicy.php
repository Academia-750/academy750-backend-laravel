<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Subtopic;
use App\Models\User;

class SubtopicPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-subtopics');
    }

    public function read(User $user, Subtopic $subtopic): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function create(User $user): bool
    {
        return $user->can('create-subtopic');
    }

    public function update(User $user, Subtopic $subtopic): bool
    {
        return $user->can('edit-subtopic');
    }

    public function delete(User $user, Subtopic $subtopic): bool
    {
        return $user->can('delete-subtopic');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-subtopics');
    }

    public function export_records(User $user): bool
    {
        return true;
    }
    public function import_records(User $user): bool
    {
        return true;
    }
}
