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
        return $user->can('list-uncompleted-tests');
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
        return $user->can('resolve-a-tests') &&
            $test?->is_solved_test === 'no' &&
            $test->user?->getRouteKey() === $user->getRouteKey();
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
    public function create_a_quiz(User $user, Test $test, Opposition $opposition, Request $request ): bool
    {
        \Log::debug('create a quiz Policy Test');
        $topicsBelongsToOpposition = true;

        $topics_id_by_opposition = $opposition->topics()->pluck('id')->toArray();

        foreach ($request->get('topics') as $topic_id) {
            if ( !in_array($topic_id, $topics_id_by_opposition, true) ) {
                $topicsBelongsToOpposition = false;
            }
        }

        return $user->can('create-tests-for-resolve') && (bool) $topicsBelongsToOpposition;
    }
}
