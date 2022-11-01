<?php
namespace App\Core\Resources\Students\v1\Interfaces;

use App\Models\Student;

interface StudentsInterface
{
    public function index();
    public function create( $request );
    public function read( $student );
    public function update($request, $student );
    public function delete( $student );
    public function mass_selection_for_action( $request );
    public function export_records( $request );
    public function import_records( $request );
}
