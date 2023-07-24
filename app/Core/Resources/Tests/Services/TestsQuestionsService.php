<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

class TestsQuestionsService
{
    public static function getQuestionsDataTestSortByIndexByTest ($test): array {
        $questions = collect([]);

        $questionsQuery = $test->questions()->orderBy('index', 'ASC')->get();

        foreach ($questionsQuery as $question_test) {

            $questions->push([
                "index" => $question_test?->pivot?->index,
                "status_question" => $question_test?->pivot?->status_solved_question,
                "question" => Question::query()->findOrFail($question_test?->pivot?->question_id),
                'question_id' => $question_test?->pivot?->question_id,
                'answer_id' => $question_test?->pivot?->answer_id,
            ]);
        }

        return $questions->sortBy('index')->values()->toArray();
    }

    public static function getQuestionsEloquentSortByIndexByTest ($test)
    {
        $instanceEloquentCollection = new \Illuminate\Database\Eloquent\Collection;

        $questionsDataTest = $test->questions()->orderBy('index', 'ASC')->jsonPaginate()/*->pluck('questions.id')*/->toArray();
        $questionsDataTestCount = $test->questions()->count();

        foreach ($questionsDataTest['data'] as $question_test) {
            $instanceEloquentCollection->add(
                new Question(
                    Question::query()->findOrFail($question_test['question_id'])?->toArray()
                )
            );
        }

        //return $instanceEloquentCollection;

        //return new \Illuminate\Pagination\Paginator();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $instanceEloquentCollection,
            $questionsDataTestCount,
            request('page.size'),
            request('page.number')
        );

    }
}
