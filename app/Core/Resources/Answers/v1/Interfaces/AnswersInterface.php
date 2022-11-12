<?php
namespace App\Core\Resources\Answers\v1\Interfaces;

use App\Models\Answer;

interface AnswersInterface
{
    public function index();
    public function create( $request );
    public function read( $answer );
    public function update($request, $answer );
    public function delete( $answer );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
