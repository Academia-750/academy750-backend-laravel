<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class StudentLessonOnlineRequest extends FormRequest
{
    public function authorize(): bool
    {

        return $this->user()->can(Permission::SEE_LESSONS)
            && $this->user()->can(Permission::SEE_ONLINE_LESSON);
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