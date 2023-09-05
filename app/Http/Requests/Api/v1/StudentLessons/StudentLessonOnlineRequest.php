<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use Database\Seeders\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class StudentLessonOnlineRequest extends FormRequest
{
    public function authorize(): bool
    {

        return $this->user()->can(Permissions::SEE_LESSONS)
            && $this->user()->can(Permissions::SEE_ONLINE_LESSON);
    }

    public function rules(): array
    {
        return [];
    }

    public function queryParameters()
    {
        return [];
    }
}