<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Test;
use App\Models\User;

class TestPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return true;
    }

    public function read(User $user, Test $test): bool
    {
        return true;
    }

    public function generate( Test $test, $request ): bool
    {
        return true;
    }
}
