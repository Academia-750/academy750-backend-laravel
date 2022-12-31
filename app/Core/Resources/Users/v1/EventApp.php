<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Notifications\Api\SendCredentialsUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventApp implements UsersInterface
{
    protected CacheApp $cacheApp;

    public function __construct(\App\Core\Resources\Users\v1\CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );

        $itemCreatedInstance["user"]->notify(new SendCredentialsUserNotification([
            'password_generated' => $itemCreatedInstance['password_generated']
        ]));

        return $itemCreatedInstance["user"];
    }

    public function read( $user ){
        return $this->cacheApp->read( $user );
    }

    public function update( $request, $user ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $user );
        /* broadcast(new UpdateUserEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $user ): void{
        /* broadcast(new DeleteUserEvent($user)); */
        $this->cacheApp->delete( $user );
    }

    public function mass_selection_for_action( $request ): array{

        /* $records = User::whereIn('id', $request->get('students'));

        broadcast(
            new ActionForMassiveSelectionUserEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ){
        $this->cacheApp->import_records( $request );
    }

    public function enable_account($request, $user)
    {
        return $this->cacheApp->enable_account( $request, $user );
    }

    public function disable_account($request, $user)
    {
        return $this->cacheApp->disable_account( $request, $user );
    }

    public function contactsUS($request)
    {
        return $this->cacheApp->contactsUS($request);
    }

    public function get_history_statistical_data_graph_by_student($request)
    {
        return $this->cacheApp->get_history_statistical_data_graph_by_student($request);
    }

    public function fetch_history_questions_by_type_and_period()
    {
        return $this->cacheApp->fetch_history_questions_by_type_and_period();
    }

    public function fetch_history_questions_wrong_by_topic_of_student($topic)
    {
        return $this->cacheApp->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    public function fetch_history_tests_completed_by_student()
    {
        return $this->cacheApp->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests()
    {
        return $this->cacheApp->fetch_topics_available_in_tests();
    }

    public function fetch_tests_between_period_date()
    {
        return $this->cacheApp->fetch_tests_between_period_date();
    }
}
