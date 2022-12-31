<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CacheApp implements UsersInterface
{
    protected DBApp $dbApp;

    public function __construct(\App\Core\Resources\Users\v1\DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        /*$nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'user.get.all' : $nameCache = json_encode(request()->query(), JSON_THROW_ON_ERROR);

        return Cache::store('redis')->tags('user')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });*/

        return $this->dbApp->index();

    }

    public function create( $request ){

        //Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $user ){

        /*return Cache::store('redis')->tags('user')->rememberForever("user.find.".$user->getRouteKey(), function () use ( $user ) {
            return $this->dbApp->read( $user );
        });*/

        return $this->dbApp->read( $user );
    }

    public function update( $request, $user ){

        //Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->update( $request, $user );
    }

    public function delete( $user ): void{

        //Cache::store('redis')->tags('user')->flush();
        $this->dbApp->delete( $user );
    }

    public function mass_selection_for_action( $request ): array{

        //Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->dbApp->export_records( $request );
    }

    public function import_records( $request ){
        //Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->import_records( $request );
    }

    public function disable_account($request, $user)
    {
        return $this->dbApp->disable_account( $request, $user );
    }

    public function enable_account($request, $user)
    {
        return $this->dbApp->enable_account( $request, $user );
    }

    public function contactsUS($request)
    {
        //Cache::store('redis')->tags('user')->flush();
        return $this->dbApp->contactsUS($request);
    }

    public function get_history_statistical_data_graph_by_student($request)
    {
        return $this->dbApp->get_history_statistical_data_graph_by_student($request);
    }

    public function fetch_history_questions_by_type_and_period()
    {
        return $this->dbApp->fetch_history_questions_by_type_and_period();
    }

    public function fetch_history_questions_wrong_by_topic_of_student($topic)
    {
        return $this->dbApp->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    public function fetch_history_tests_completed_by_student()
    {
        return $this->dbApp->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests()
    {
        return $this->dbApp->fetch_topics_available_in_tests();
    }

    public function fetch_tests_between_period_date()
    {
        return $this->dbApp->fetch_tests_between_period_date();
    }
}
