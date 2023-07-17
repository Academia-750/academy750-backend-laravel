<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Authorizer implements UsersInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(\App\Core\Resources\Users\v1\SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): \App\Http\Resources\Api\User\v1\UserCollection
    {
        Gate::authorize('index', User::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', User::class );
        return $this->schemaJson->create($request);
    }

    public function read( $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('read', $user );
        return $this->schemaJson->read( $user );
    }

    public function update( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('update', $user );
        return $this->schemaJson->update( $request, $user );
    }

    public function delete( $user ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $user );
        return $this->schemaJson->delete( $user );
    }

    public function mass_selection_for_action( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', User::class );
        return $this->schemaJson->mass_selection_for_action( $request );
    }

    public function disable_account( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('disable_account', $user );
        return $this->schemaJson->disable_account( $request, $user );
    }

    public function enable_account( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('enable_account', $user );
        return $this->schemaJson->enable_account( $request, $user );
    }

    public function export_records( $request ){
        Gate::authorize('export_records', User::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ){
        Gate::authorize('import_records', User::class );
        return $this->schemaJson->import_records( $request );
    }

    public function contactsUS($request)
    {
        return $this->schemaJson->contactsUS($request);
    }

    public function get_history_statistical_data_graph_by_student($request)
    {
        Gate::authorize('get_history_statistical_data_graph_by_student', User::class);
        return $this->schemaJson->get_history_statistical_data_graph_by_student($request);
    }

    public function fetch_history_questions_by_type_and_period()
    {
        $data = [
            'test_id' => request('test-id'),
            'type_question' => request('type-question'),
        ];

        $validateData = Validator::make($data,[
            'test_id' => ['required', 'exists:tests,uuid'],
            'type_question' => ['required', Rule::in(['correct','wrong', 'unanswered'])],
        ]);

        if ($validateData->fails()) {
            abort(400, 'Por favor, mandar los parámetros correctos, {test_id} y {type_question}');
        }

        Gate::authorize('fetch_history_questions_by_type_and_period', User::class);
        return $this->schemaJson->fetch_history_questions_by_type_and_period();
    }

    public function fetch_history_questions_wrong_by_topic_of_student($topic)
    {
        Gate::authorize('fetch_history_questions_wrong_by_topic_of_student', User::class);
        return $this->schemaJson->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    public function fetch_history_tests_completed_by_student()
    {
        Gate::authorize('fetch_history_tests_completed_by_student', User::class);
        return $this->schemaJson->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests()
    {
        Gate::authorize('fetch_topics_available_in_tests', User::class);
        return $this->schemaJson->fetch_topics_available_in_tests();
    }

    public function fetch_tests_between_period_date()
    {
        Gate::authorize('fetch_tests_between_period_date', User::class);
        return $this->schemaJson->fetch_tests_between_period_date();
    }
}
