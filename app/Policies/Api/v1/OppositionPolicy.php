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
        return $user->hasRole('admin');
    }

    public function update(User $user, Opposition $opposition): bool
    {
        return $user->hasRole('admin') && $opposition->isAvailable();
    }

    public function delete(User $user, Opposition $opposition): bool
    {
        return $user->hasRole('admin') && $opposition->isAvailable();
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->hasRole('admin');
    }
}