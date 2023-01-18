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
                "category" => $this->resource->category,
                "total_number_of_records" => $this->resource->total_number_of_records,
                "total_number_failed_records" => $this->resource->total_number_failed_records,
                "total_number_successful_records" => $this->resource->total_number_successful_records,
                "status_process_file" => $this->resource->status_process_file,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [

            ]
        ];
    }
}
