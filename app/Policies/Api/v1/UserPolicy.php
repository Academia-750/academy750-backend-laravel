<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function read(User $user, User $userResource): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $userResource): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, User $userResource): bool
    {
        return $user->hasRole('admin');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function enable_account(User $user, User $userResource): bool
    {
        return $user->hasRole('admin');
    }

    public function disable_account(User $user, User $userResource): bool
    {
        return $user->hasRole('admin');
    }

    public function export_records(User $user): bool
    {
        return $user->hasRole('admin');
    }
    public function import_records(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function get_history_statistical_data_graph_by_student(User $user): bool
    {
        return $user->hasRole('student');
    }

    public function fetch_history_questions_by_type_and_period(User $user): bool
    {
        return $user->hasRole('student');
    }

    public function fetch_history_questions_wrong_by_topic_of_student(User $user): bool
    {
        return $user->hasRole('student');
    }

    public function fetch_history_tests_completed_by_student(User $user): bool
    {
        return $user->hasRole('student');
    }

    public function fetch_topics_available_in_tests(User $user): bool
    {
        return $user->hasRole('student');
    }

    public function fetch_tests_between_period_date(User $user): bool
    {
        return $user->hasRole('student');
    }
}