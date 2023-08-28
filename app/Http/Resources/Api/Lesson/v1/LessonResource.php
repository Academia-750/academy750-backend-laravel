<?php

namespace App\Http\Resources\Api\Lesson\v1;

use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{

    public static $wrap = 'result';


    public $collects = Lesson::class;
}