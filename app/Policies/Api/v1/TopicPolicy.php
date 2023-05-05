<?php

namespace App\Policies\Api\v1;

use App\Models\Opposition;
use App\Models\Question;
use App\Models\Subtopic;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-topics');
    }

    public function read(User $user, Topic $topic): bool
    {
        return $user->can('see-a-topic');
    }

    public function create(User $user): bool
    {
        return $user->can('create-topic');
    }

    public function update(User $user, Topic $topic): bool
    {
        return $user->can('edit-topic') && $topic->isAvailable();
    }

    public function delete(User $user, Topic $topic): bool
    {
        return $user->can('delete-topic') && $topic->isAvailable();
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-topics');
    }

    public function get_relationship_subtopics (User $user, Topic $topic): bool {
        return $user->can("see-a-topic");
    }


    public function get_relationship_oppositions (User $user, Topic $topic): bool {
        return $user->can("see-a-topic");
    }

    public function get_relationship_a_subtopic (User $user, Topic $topic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable();
    }
    public function get_relationship_subtopics_by_opposition (User $user, Topic $topic, Opposition $opposition): bool {
        return $user->can("see-a-topic") && $topic->isAvailable() && $opposition->isAvailable();
    }

    public function get_relationship_questions (User $user, Topic $topic): bool {
        return $user->can("see-a-topic") /*&& $topic->isAvailable()*/;
    }

    public function get_relationship_a_question (User $user, Topic $topic): bool {
        return $user->can("see-a-topic")/* && $topic->isAvailable()*/;
    }

    public function subtopics_get_relationship_questions (User $user, Topic $topic, Subtopic $subtopic): bool {
        return $user->can("see-a-topic")/* && $topic->isAvailable() && $subtopic->isAvailable()*/;
    }

    public function subtopics_get_relationship_a_question (User $user, Topic $topic, Subtopic $subtopic): bool {
        return $user->can("see-a-topic") /*&& $topic->isAvailable() && $subtopic->isAvailable()*/;
    }

    public function create_relationship_subtopic (User $user, Topic $topic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable();
    }

    public function update_relationship_subtopic (User $user, Topic $topic, Subtopic $subtopic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable() && $subtopic->isAvailable();
    }

    public function delete_relationship_subtopic (User $user, Topic $topic, Subtopic $subtopic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable() && $subtopic->isAvailable();
    }

    public function get_oppositions_available_of_topic (User $user, Topic $topic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable();
    }

    public function assign_opposition_with_subtopics_to_topic (User $user, Topic $topic): bool {
        return $user->can("see-a-topic") && $topic->isAvailable();
    }

    public function update_subtopics_opposition_by_topic (User $user, Topic $topic, Opposition $opposition): bool {
        return $user->can("see-a-topic") && $topic->isAvailable() && $opposition->isAvailable();
    }

    public function delete_opposition_by_topic (User $user, Topic $topic, Opposition $opposition): bool {
        return $user->can("see-a-topic") && $topic->isAvailable() && $opposition->isAvailable();
    }

    public function topic_get_relationship_questions(User $user, Topic $topic): bool
    {
        return $user->can('see-a-topic') && $topic->isAvailable();
    }

    public function topic_get_a_question(User $user, Topic $topic, Question $question): bool
    {
        if (!in_array($question->getRouteKey(), $topic->questions->pluck('id')->toArray(), true)) {
            abort(404);
        }

        return $user->can('see-a-topic');
    }

    public function topic_create_a_question(User $user, Topic $topic): bool
    {
        return $user->can('see-a-topic') && $topic->isAvailable();
    }

    public function topic_update_a_question(User $user, Topic $topic, Question $question): bool
    {
        return $user->can('see-a-topic') && $topic->isAvailable() && $question->isVisible();
    }

    public function topic_delete_a_question(User $user, Topic $topic, Question $question): bool
    {
        return $user->can('see-a-topic') && $topic->isAvailable() && $question->isVisible();
    }

    public function export_records(User $user): bool
    {
        return true;
    }
    public function import_records(User $user): bool
    {
        return true;
    }

    public function topic_relationship_questions(): bool
    {
        return true;
    }
}
