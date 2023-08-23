<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

class QueryParametersQuestionsForResolveTest
{
    public static function getQuestionsDataTestSortByIndexByTest($test): array
    {
        $questions = collect([]);

        $questionsQuery = $test->questions()->orderBy('index', 'ASC')->get();

        foreach ($questionsQuery as $question_test) {

            $questions->push([
                "index" => $question_test?->pivot?->index,
                "status_question" => $question_test?->pivot?->status_solved_question,
                "question" => Question::query()->findOrFail($question_test?->pivot?->question_id),
                'question_id' => $question_test?->pivot?->question_id,
                'answer_id' => $question_test?->pivot?->answer_id ? Answer::query()->findOrFail($question_test?->pivot?->answer_id)?->getRouteKey() : null,
            ]);
        }

        return $questions->sortBy('index')->values()->toArray();
    }

    public static function getQuestionsEloquentSortByIndexByTest($test)
    {
        $instanceEloquentCollection = new \Illuminate\Database\Eloquent\Collection;

        $questionsDataTest = $test->questions()->orderBy('index', 'ASC')->jsonPaginate()->toArray();
        $questionsDataTestCount = $test->questions()->count();


        foreach ($questionsDataTest['data'] as $question_test) {
            $instanceEloquentCollection->add(
                new Question(
                    Question::query()->findOrFail($question_test['question_id'])?->toArray()
                )
            );
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $instanceEloquentCollection,
            $questionsDataTestCount,
            request('page.size'),
            request('page.number')
        );

    }
}