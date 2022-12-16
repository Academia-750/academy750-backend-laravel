<?php

namespace App\Policies\Api\v1;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function subtopics_relationship_get_questions (User $user, $question, $subtopic): bool {
        return true;
    }
    public function subtopic_relationship_questions_read (User $user, $subtopic, $question): bool {

        return $subtopic->questions()->pluck("id")->contains($question->id);
    }
    public function subtopic_relationship_questions_create (User $user, $subtopic): bool {
        return true;
    }
    public function subtopic_relationship_questions_update (User $user, $subtopic, $question): bool {
        return true;
    }
    public function subtopic_relationship_questions_delete (User $user, $subtopic, $question): bool {
        return true;
    }
    public function topics_relationship_get_questions (User $user, $question, $topic): bool {
        return true;
    }
    public function topic_relationship_questions_read (User $user, $topic, $question): bool {
        return $topic->questions()->pluck("id")->contains($question->id);
    }
    public function topic_relationship_questions_create (User $user, $topic): bool {
        return true;
    }
    public function topic_relationship_questions_update (User $user, $topic, $question): bool {
        return true;
    }
    public function topic_relationship_questions_delete (User $user, $topic, $question): bool {
        return true;
    }

    public function generate(): bool
    {
        return true;
    }
}
