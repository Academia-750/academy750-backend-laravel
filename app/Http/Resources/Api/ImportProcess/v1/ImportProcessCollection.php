<?php

namespace App\Http\Resources\Api\ImportProcess\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ImportProcessCollection extends ResourceCollection
{
    //public $collects = ExampleResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'utilities' => []
        ];
    }
}
