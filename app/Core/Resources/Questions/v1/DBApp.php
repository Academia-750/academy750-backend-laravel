<?php
namespace App\Core\Resources\Questions\v1;

use App\Imports\Api\v1\QuestionsImport;
use App\Models\Answer;
use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\Api\Questions\v1\QuestionsImport;
use App\Exports\Api\Questions\v1\QuestionsExport;


class DBApp implements QuestionsInterface
{
    protected Question $model;

    public function __construct(Question $question ){
        $this->model = $question;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {

        $questions_id = $subtopic->questions()->pluck("id");

        return $this->model->query()->whereIn('id', $questions_id->toArray())->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        return $subtopic->questions()->firstWhere("id", "=", $question->getRouteKey())->applyIncludes();
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        $question = $subtopic->questions()->create([
            'question' => $request->get('question-text'),
            'reason' => $request->get('reason-question'),
            'is_visible' => (bool) $request->get('is-visible') ? 'yes' : 'no',
            "its_for_test" => (bool) $request->get('is-test') ? 'yes' : 'no',
            "its_for_card_memory" => (bool) $request->get('is-card-memory') ? 'yes' : 'no',
        ]);

        if ($request->get('file-reason')) {
            $question->image()->create([
                'path' => Storage::disk('public')->put(
                    'questions', file_get_contents($request->file('file-reason'))
                ),
                'type_path' => 'local'
            ]);
        }

        $answers = [
            [
                'answer' => $request->get('answer-correct'),
                'is_grouper_answer' => $request->get('is-grouper-answer-correct') ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-one'),
                'is_grouper_answer' => $request->get('is-grouper-answer-one') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-two'),
                'is_grouper_answer' => $request->get('is-grouper-answer-two') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-three'),
                'is_grouper_answer' => $request->get('is-grouper-answer-three') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
        ];

        shuffle($answers);

        foreach ($answers as $answer) {
            Answer::query()->create([
                'answer' => $answer["answer"],
                'is_grouper_answer' => $answer["is_grouper_answer"],
                'is_correct_answer' => $answer["is_correct_answer"],
                'question_id' => $answer["question_id"],
            ]);
        }

        return Question::query()->applyIncludes()->find($question->getRouteKey());
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        $question->question = $request->get('question-text');
        $question->reason = $request->get('reason-question');
        $question->is_visible = (bool) $request->get('is-visible') ? 'yes' : 'no';
        $question->its_for_test = (bool) $request->get('is-test') ? 'yes' : 'no';
        $question->its_for_card_memory = (bool) $request->get('is-card-memory') ? 'yes' : 'no';
        $question->save();

        /*$question->update([
            'question' => $request->get('question-text'),
            'reason' => $request->get('reason-question'),
            'is_visible' => $request->get('is-visible'),
            "its_for_test" => $request->get('is-test'),
            "its_for_card_memory" => $request->get('is-card-memory'),
        ]);*/

        $answers = [
            [
                'id' => $request->get('answer-correct-id'),
                'answer' => $request->get('answer-correct'),
                'is_grouper_answer' => $request->get('is-grouper-answer-correct') ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-one-id'),
                'answer' => $request->get('answer-one'),
                'is_grouper_answer' => $request->get('is-grouper-answer-one') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-two-id'),
                'answer' => $request->get('answer-two'),
                'is_grouper_answer' => $request->get('is-grouper-answer-two') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-three-id'),
                'answer' => $request->get('answer-three'),
                'is_grouper_answer' => $request->get('is-grouper-answer-three') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
        ];

        shuffle($answers);

        foreach ($answers as $answer) {
            $answer = Answer::query()->find($answer);
            $answer->answer = $answer["answer"];
            $answer->is_grouper_answer = $answer["is_grouper_answer"];
            $answer->is_correct_answer = $answer["is_correct_answer"];
            $answer->save();

            /*$answer->update([
                'answer' => $answer["answer"],
                'is_grouper_answer' => $answer["is_grouper_answer"],
                'is_correct_answer' => $answer["is_correct_answer"]
            ]);*/
        }

        return Question::query()->applyIncludes()->find($question->getRouteKey());

    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        // TODO: Implement subtopic_relationship_questions_delete() method.
    }

    public function topics_relationship_get_questions($topic)
    {
        $questions_id = $topic->questions()->pluck("id");

        return $this->model->query()->whereIn('id', $questions_id->toArray())->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        return $topic->questions()->firstWhere("id", "=", $question->getRouteKey())->applyIncludes();
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        $question = $topic->questions()->create([
            'question' => $request->get('question-text'),
            'reason' => $request->get('reason-question'),
            'is_visible' => (bool) $request->get('is-visible') ? 'yes' : 'no',
            "its_for_test" => (bool) $request->get('is-test') ? 'yes' : 'no',
            "its_for_card_memory" => (bool) $request->get('is-card-memory') ? 'yes' : 'no',
        ]);

        $answers = [
            [
                'answer' => $request->get('answer-correct'),
                'is_grouper_answer' => $request->get('is-grouper-answer-correct') ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-one'),
                'is_grouper_answer' => $request->get('is-grouper-answer-one') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-two'),
                'is_grouper_answer' => $request->get('is-grouper-answer-two') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'answer' => $request->get('answer-three'),
                'is_grouper_answer' => $request->get('is-grouper-answer-three') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
        ];

        shuffle($answers);

        foreach ($answers as $answer) {
            Answer::query()->create([
                'answer' => $answer["answer"],
                'is_grouper_answer' => $answer["is_grouper_answer"],
                'is_correct_answer' => $answer["is_correct_answer"],
                'question_id' => $answer["question_id"],
            ]);
        }

        return Question::query()->applyIncludes()->find($question->getRouteKey());
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        $question->question = $request->get('question-text');
        $question->reason = $request->get('reason-question');
        $question->is_visible = (bool) $request->get('is-visible') ? 'yes' : 'no';
        $question->its_for_test = (bool) $request->get('is-test') ? 'yes' : 'no';
        $question->its_for_card_memory = (bool) $request->get('is-card-memory') ? 'yes' : 'no';
        $question->save();

        /*$question->update([
            'question' => $request->get('question-text'),
            'reason' => $request->get('reason-question'),
            'is_visible' => $request->get('is-visible'),
            "its_for_test" => $request->get('is-test'),
            "its_for_card_memory" => $request->get('is-card-memory'),
        ]);*/

        $answers = [
            [
                'id' => $request->get('answer-correct-id'),
                'answer' => $request->get('answer-correct'),
                'is_grouper_answer' => $request->get('is-grouper-answer-correct') ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-one-id'),
                'answer' => $request->get('answer-one'),
                'is_grouper_answer' => $request->get('is-grouper-answer-one') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-two-id'),
                'answer' => $request->get('answer-two'),
                'is_grouper_answer' => $request->get('is-grouper-answer-two') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-three-id'),
                'answer' => $request->get('answer-three'),
                'is_grouper_answer' => $request->get('is-grouper-answer-three') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
        ];

        shuffle($answers);

        foreach ($answers as $answer) {
            $answer = Answer::query()->find($answer);
            $answer->answer = $answer["answer"];
            $answer->is_grouper_answer = $answer["is_grouper_answer"];
            $answer->is_correct_answer = $answer["is_correct_answer"];
            $answer->save();

            /*$answer->update([
                'answer' => $answer["answer"],
                'is_grouper_answer' => $answer["is_grouper_answer"],
                'is_correct_answer' => $answer["is_correct_answer"]
            ]);*/
        }

        return Question::query()->applyIncludes()->find($question->getRouteKey());
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        // TODO: Implement topic_relationship_questions_delete() method.
    }
}
