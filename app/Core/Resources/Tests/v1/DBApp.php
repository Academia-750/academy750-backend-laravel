<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;


class DBApp implements TestsInterface
{
    protected Test $model;

    public function __construct(Test $test ){
        $this->model = $test;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function read( $test ): \App\Models\Test{
        return $this->model->applyIncludes()->find($test->getRouteKey());
    }

    public function generate( $request ){
        $questions_count = Question::isVisible()->applyFilters()->applySorts()->applyIncludes()->count();
        if($questions_count < request('filter')['take']){
            $questions = Question::where('is_visible', 'no')->get();
            $questions->map(fn (Question $question) => $question->update(['is_visible' => 'yes']));
        };

        $questions = Question::isVisible()->applyFilters()->applySorts()->applyIncludes()->get();
        $questions->map(fn (Question $question) => $question->update(['is_visible' => 'no']));
        return $questions;
    }
}
