<?php

namespace App\Http\Resources\Api\Group\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{

    public static $wrap = 'result';


    public $collects = Group::class;

}