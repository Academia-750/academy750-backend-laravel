<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class StudentLessonJoinRequest extends FormRequest
{
    public function authorize(): bool
    {

        return $this->user()->can(Permission::SEE_LESSONS)
            && $this->user()->can(Permission::JOIN_LESSONS);
    }

    public function rules(): array
    {
        return [

            'join' => [
                'boolean',
                'required',
            ]
        ];
    }

    public function bodyParameters()
    {
        return [
            'join' => [
                'description' => 'Boolean that sets to true or false if the user will be joining the lesson. Setting to true will enable the flag `will_join`',
                'example' => 'true'
            ]
        ];
    }
}