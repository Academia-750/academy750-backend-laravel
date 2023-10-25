<?php

namespace App\Http\Requests\Api\v1\Lesson;


use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListLessonStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()->hasRole('admin')) {
            return true;
        }
        // Not admin can also see this list if the have the proper permissions
        return $this->user()->can(Permission::SEE_LESSONS)
            && $this->user()->can(Permission::SEE_LESSON_PARTICIPANTS);
    }

    private $orderBy = ['full_name', 'dni', 'group_name', 'created_at', 'updated_at'];

    public function rules(): array
    {
        return [
            'orderBy' => [
                'string',
                Rule::in($this->orderBy)
            ],
            'willJoin' => [
                'boolean',
            ],
            'order' => [
                Rule::in([1, -1])
            ],
            'limit' => [
                'integer',
                'min:0'
            ],
            'offset' => [
                'integer',
                'min:0'
            ],
            'content' => [
                'string'
            ]
        ];
    }

    public function queryParameters()
    {
        return [
            'willJoin' => [
                'description' => 'Filter by the users that will be join or not',
                'example' => true,
            ],
            'orderBy' => [
                'description' => 'Property to order by ' . join($this->orderBy),
                'example' => 'created_at'
            ],
            'order' => [
                'description' => 'Order 1 ASC -1 DESC',
                'example' => -1,
            ],
            'limit' => [
                'description' => 'Limit of records returned (Pagination)',
                'example' => 10,
            ],
            'offset' => [
                'description' => 'Offset of records to be skipped (page*limit) (Pagination)',
                'example' => 0,
            ],
            'content' => [
                'description' => 'Search by substring match (user name, dni, or group name)',
                'example' => '',
            ],
        ];
    }
}
