<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as PermissionSpatieModel;

class Permission extends PermissionSpatieModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

}
