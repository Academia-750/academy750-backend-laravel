<?php

namespace App\Http\Resources\Api\ImportRecord\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'resources',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "number_of_row" => $this->resource->number_of_row,
                "reference_number" => $this->resource->reference_number,
                "has_errors" => $this->resource->has_errors === 'yes',
                "errors_validation" => $this->resource->errors_validation,
            ],
            'relationships' => [

            ]
        ];
    }
}
