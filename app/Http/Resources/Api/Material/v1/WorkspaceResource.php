<?php

namespace App\Http\Resources\Api\Material\v1;

use App\Models\Workspace;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{

    public static $wrap = 'result';


    public $collects = Workspace::class;

}