<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\TopicGroup;
use App\Models\User;

class TopicGroupPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-topic-groups');
    }

    public function read(User $user, TopicGroup $topicGroup): bool
    {
        return $user->can('see-a-resource');
    }

    public function create(User $user): bool
    {
        return $user->can('create-resource');
    }

    public function update(User $user, TopicGroup $topicGroup): bool
    {
        return $user->can('edit-resource');
    }

    public function delete(User $user, TopicGroup $topicGroup): bool
    {
        return $user->can('delete-resource');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-resources');
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
