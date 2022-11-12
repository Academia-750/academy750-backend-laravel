<?php
namespace App\Core\Resources\Questions\v1\Interfaces;

use App\Models\Question;

interface QuestionsInterface
{
    public function index();
    public function create( $request );
    public function read( $question );
    public function update($request, $question );
    public function delete( $question );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
