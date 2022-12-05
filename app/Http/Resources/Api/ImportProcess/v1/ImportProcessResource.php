<?php

namespace App\Http\Resources\Api\ImportProcess\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportProcessResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'import-processes',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "name_file" => $this->resource->name_file,
                "total_number_of_records" => $this->resource->total_number_of_records,
                "status_process_file" => $this->resource->status_process_file,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => [

            ]
        ];
    }
}
