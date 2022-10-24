<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as RoleSpatieModel;

class Role extends RoleSpatieModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

}
