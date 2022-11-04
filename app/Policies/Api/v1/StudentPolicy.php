<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-students');
    }

    public function read(User $user, Student $student): bool
    {
        return $user->can('see-a-student');
    }

    public function create(User $user): bool
    {
        return $user->can('create-student');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('edit-student');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('delete-student');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('ban-student');
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
