<?php

namespace App\Http\Resources\Api\Questionnaire\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionnaireCollection extends ResourceCollection
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
