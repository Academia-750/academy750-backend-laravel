<?php

namespace App\Http\Resources\Api\TopicGroup\v1;

use App\Http\Resources\Api\Topic\v1\TopicCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicGroupResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'topic_group',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'description' => $this->resource->description,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'topics' => $this->when(collect($this->resource)->has('topic'),
                    function () {
                        return TopicCollection::make($this->resource->topics);
                    })
            ]
        ];
    }
}
