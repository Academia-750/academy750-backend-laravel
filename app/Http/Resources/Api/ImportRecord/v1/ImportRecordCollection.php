<?php

namespace App\Http\Resources\Api\ImportRecord\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ImportRecordCollection extends ResourceCollection
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
