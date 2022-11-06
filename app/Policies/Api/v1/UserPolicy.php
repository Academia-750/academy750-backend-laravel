<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-students');
    }

    public function read(User $user, User $userResource): bool
    {
        return $user->can('see-a-student');
    }

    public function create(User $user): bool
    {
        return $user->can('create-student');
    }

    public function update(User $user, User $userResource): bool
    {
        return $user->can('edit-student');
    }

    public function delete(User $user, User $userResource): bool
    {
        return $user->can('delete-student');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-users');
    }

    public function enable_account(User $user, User $userResource): bool
    {
        return $user->can('disable-account-student');
    }

    public function disable_account(User $user, User $userResource): bool
    {
        return $user->can('enable-account-student');
    }

    public function export_records(User $user): bool
    {
        return $user->can('export-students');
    }
    public function import_records(User $user): bool
    {
        return $user->can('import-students');
    }
}
