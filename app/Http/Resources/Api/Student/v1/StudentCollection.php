<?php

namespace App\Http\Resources\Api\Student\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Collection;

class StudentCollection extends ResourceCollection
{
    //public $collects = ExampleResource::class;

    #[ArrayShape(['data' => Collection::class])] public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
