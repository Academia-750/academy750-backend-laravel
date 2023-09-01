<?php

namespace App\Models;

use App\Core\Services\UUIDTrait;
use Spatie\Permission\Models\Permission as PermissionSpatieModel;

class Permission extends PermissionSpatieModel
{
    use UUIDTrait;

    public $keyType = "string";
    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "id",
        'name',
        'alias_name',
        'guard_name',
    ];

    protected $casts = [
        'id' => 'string'
    ];

}