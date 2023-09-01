<?php

namespace App\Http\Resources\Api\Role\v1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Single Role resource, used in the phase 2
 */
class RoleItemResource extends JsonResource
{
    public static $wrap = 'result';


    public $collects = Role::class;
}