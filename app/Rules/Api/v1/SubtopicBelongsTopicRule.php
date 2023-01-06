<?php

namespace App\Rules\Api\v1;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class SubtopicBelongsTopicRule implements Rule
{
    private $topic_uuid;
    private $topics;

    public function __construct($topic_uuid, $topics)
    {
        //
        $this->topic_uuid = $topic_uuid;
        $this->topics = $topics;
    }

    /**
     * Valida que el Subtema pertenece al Tema especificado
     *
     * Para saber el subtema y el tema, se pasan sus UUID y luego se consulta su informacion.
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $topic = $this->topics->where('id', '=', $this->topic_uuid)->first();

        return $topic->subtopics->contains($value);
    }

    public function message(): string
    {
        return "El subtema no pertenece a este tema";
    }
}
