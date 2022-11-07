<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Opposition;
use App\Models\User;

class OppositionPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return true;
    }

    public function read(User $user, Opposition $opposition): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Opposition $opposition): bool
    {
        return true;
    }

    public function delete(User $user, Opposition $opposition): bool
    {
        return true;
    }

    public function mass_selection_for_action(User $user): bool
    {
        return true;
    }

    public function export_records(User $user): bool
    {
        return true;
    }
    public function import_records(User $user): bool
    {
        return true;
    }

    public function get_companies_archived(User $user): bool
    {
        return true;
    }
    public function restore_archived(User $user, $opposition): bool
    {
        return true;
    }
    public function force_delete_archived(User $user, $opposition): bool
    {
        return true;
    }
}
