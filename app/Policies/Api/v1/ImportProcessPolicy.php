<?php

namespace App\Policies\Api\v1;

use App\Models\ImportProcess;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class ImportProcessPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return true;
    }

    public function get_relationship_import_records(User $user, ImportProcess $importProcess): bool
    {
        return true;
    }
}
