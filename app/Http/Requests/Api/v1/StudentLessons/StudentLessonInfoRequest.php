<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Material;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentLessonInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::SEE_LESSONS);
    }

}