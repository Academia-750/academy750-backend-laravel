<?php

namespace App\Http\Resources\Api\Group\v1;

use App\Models\GroupUsers;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
{

    public static $wrap = 'result';


    public $collects = GroupUsers::class;

}