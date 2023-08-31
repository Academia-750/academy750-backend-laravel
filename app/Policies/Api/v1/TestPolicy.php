<?php

namespace App\Policies\Api\v1;

use App\Models\Opposition;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;

class TestPolicy
{
    use HandlesAuthorization;

    public function get_tests_unresolved(User $user): bool
    {
        return true;
    }

    public function get_cards_memory(User $user): bool
    {
        return true;
    }

    /**
     * Para poder consultar un Test ya sea de tipo Cuestionario o Tarjeta de memoria, tiene que tener los permisos
     * además de que el test no esté resuelto y además de que pertenezca al usuario que solicita verlo
     *
     * @param User $user
     * @param Test $test
     * @return bool
     */
    public function fetch_unresolved_test(User $user, Test $test): bool
    {
        return
            $test?->is_solved_test === 'no' &&
            $test?->test_type === 'test' &&
            $test->user?->getKey() === $user->getKey();
    }


    /**
     * Para poder consultar un Test ya sea de tipo Cuestionario o Tarjeta de memoria, tiene que tener los permisos
     * además de que el test no esté resuelto y además de que pertenezca al usuario que solicita verlo
     *
     * @param User $user
     * @param Test $test
     * @return bool
     */
    public function fetch_card_memory(User $user, Test $test): bool
    {
        return
            $test?->test_type === 'card_memory' &&
            $test->user?->getKey() === $user->getKey();
    }

    /**
     * Verifica que todos los temas enviados, pertenezcan forzozamente a la Oposición seleccionada
     *
     * @param User $user
     * @param Test $test
     * @param Opposition $opposition
     * @param $request
     * @return bool
     */
    public function create_a_quiz(User $user, Test $test, Opposition $opposition, Request $request): bool
    {
        // \Log::debug('create a quiz Policy Test');
        $topicsBelongsToOpposition = true;

        $topics_uuids_by_opposition = $opposition->topics()->pluck('uuid')->toArray();

        foreach ($request->get('topics') as $topic_uuid) {
            if (!in_array($topic_uuid, $topics_uuids_by_opposition, true)) {
                $topicsBelongsToOpposition = false;
            }
        }

        return (bool) $topicsBelongsToOpposition;
    }

    /**
     * @param User $user
     * @param Test $test
     * @return bool
     */
    public function fetch_test_completed(User $user, Test $test): bool
    {
        return $user->hasRole('student') &&
            (bool) $user->tests()->findOrFail($test->getKey()) && $test->is_solved_test === 'yes';
    }
}