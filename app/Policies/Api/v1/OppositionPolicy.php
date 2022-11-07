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
        return $user->can('list-oppositions');
    }

    public function read(User $user, Opposition $opposition): bool
    {
        return $user->can('see-a-opposition');
    }

    public function create(User $user): bool
    {
        return $user->can('create-opposition');
    }

    public function update(User $user, Opposition $opposition): bool
    {
        return $user->can('edit-opposition');
    }

    public function delete(User $user, Opposition $opposition): bool
    {
        return $user->can('delete-opposition');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-oppositions');
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
