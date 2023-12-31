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
        return true;
    }

    public function read(User $user, TopicGroup $topicGroup): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, TopicGroup $topicGroup): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, TopicGroup $topicGroup): bool
    {
        return $user->hasRole('admin');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->hasRole('admin');
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