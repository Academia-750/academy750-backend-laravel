<?php

namespace App\Http\Resources\Api\Material\v1;

use App\Models\Material;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{

    public static $wrap = 'result';


    public $collects = Material::class;

}