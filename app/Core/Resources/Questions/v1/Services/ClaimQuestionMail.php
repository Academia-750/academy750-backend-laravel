<?php

namespace App\Core\Resources\Questions\v1\Services;

use App\Models\Opposition;
use App\Models\Question;
use App\Models\Subtopic;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ClaimQuestionNotification;
use Illuminate\Support\Facades\Auth;

class ClaimQuestionMail
{
    public static function claimQuestion (string $test_id, string $question_id, string $claim_text): void
    {
        \Log::debug(config('mail.mail_impugnaciones'));
        $userAcademia = User::query()->firstWhere('email', '=', config('mail.mail_impugnaciones'));

        if (!$userAcademia) {
            abort(500, 'No se puede encontrar el correo de la academia');
        }

        $test = Test::findOrFail($test_id);
        $question = Question::findOrFail($question_id);

        $model = $question->questionable;

        if ( $model instanceof Subtopic) {
            $topic = Topic::findOrFail($model->topic->id);
        } else {
            $topic = $model;
        }

        $userAcademia->notify(new ClaimQuestionNotification(
            $test->opposition,
            $question,
            $topic,
            $claim_text,
            Auth::user()
        ));
    }

}
