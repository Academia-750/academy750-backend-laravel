<?php
namespace App\Core\Resources\Users\v1\Interfaces;

interface UsersInterface
{
    public function index();
    public function create( $request );
    public function read( $user );
    public function update($request, $user );
    public function delete( $user );
    public function mass_selection_for_action( $request );
    public function disable_account( $request, $user );
    public function enable_account( $request, $user );
    public function contactsUS( $request );
    public function get_history_statistical_data_graph_by_student($request);
    public function fetch_history_questions_by_type_and_period($request);
    public function fetch_history_questions_wrong_by_topic_of_student($topic);
    public function fetch_history_tests_completed_by_student();
}
