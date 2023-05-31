<?php

namespace App\Http\Resources\Api\Question\v1;

use App\Http\Resources\Api\Answer\v1\AnswerCollection;
use App\Http\Resources\Api\Image\v1\ImageResource;
use App\Http\Resources\Api\TestModel\v1\QuestionnaireCollection as TestsCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'questions',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "question-text" => $this->resource->question,
                "reason-text" => $this->resource->reason,
                "is_question_binary_alternatives" => $this->resource->is_question_binary_alternatives,
                "is_visible" => $this->resource->is_visible,
                'its_for_test' => $this->resource->its_for_test,
                'its_for_card_memory' => $this->resource->its_for_card_memory,

                "show_reason_text_in_test" => $this->resource->show_reason_text_in_test,
                "show_reason_text_in_card_memory" => $this->resource->show_reason_text_in_card_memory,
                "show_reason_image_in_test" => $this->resource->show_reason_image_in_test,
                "show_reason_image_in_card_memory" => $this->resource->show_reason_image_in_card_memory,

                "question_in_edit_mode" => $this->resource->question_in_edit_mode,

                //'its_being_used_tests' => $this->resource->its_being_used_tests,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'answers' => AnswerCollection::make($this->resource->answers),
                'tests' => $this->when(collect($this->resource)->has('tests'),
                    function () {
                        return TestsCollection::make($this->resource->tests);
                    }),
                'image' => $this->when((bool) $this->resource->image,
                    function () {
                        return ImageResource::make($this->resource->image);
                    }),
                // 'questionable' => $this->when(collect($this->resource)->has('questionable'),
                //     function () {
                //         return $this->resource->questionable;
                //     })
            ],
            'meta' => [
                'can_this_question_be_affected' => $this->resource->tests()->count() === 0 && $this->resource->is_visible === 'yes'
            ]
        ];
    }
}
