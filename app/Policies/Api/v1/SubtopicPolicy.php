<?php

namespace App\Policies\Api\v1;

use App\Models\Question;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Subtopic;
use App\Models\User;

class SubtopicPolicy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        return $user->can('list-subtopics');
    }

    public function read(User $user, Subtopic $subtopic): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function create(User $user): bool
    {
        return $user->can('create-subtopic');
    }

    public function update(User $user, Subtopic $subtopic): bool
    {
        return $user->can('edit-subtopic');
    }

    public function delete(User $user, Subtopic $subtopic): bool
    {
        return $user->can('delete-subtopic');
    }

    public function mass_selection_for_action(User $user): bool
    {
        return $user->can('action-for-multiple-subtopics');
    }

    public function subtopic_get_relationship_questions(User $user, Subtopic $subtopic): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function subtopic_get_a_question(User $user, Subtopic $subtopic, Question $question): bool
    {
        if (!in_array($question->getKey(), $subtopic->questions->pluck('id')->toArray(), true)) {
            abort(404);
        }

        return $user->can('see-a-subtopic');
    }

    public function subtopic_create_a_question(User $user, Subtopic $subtopic): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function subtopic_update_a_question(User $user, Subtopic $subtopic, Question $question): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function subtopic_delete_a_question(User $user, Subtopic $subtopic, Question $question): bool
    {
        return $user->can('see-a-subtopic');
    }

    public function export_records(User $user): bool
    {
        return true;
    }
    public function import_records(User $user): bool
    {
        return $user->can('create-subtopic');
    }
}
