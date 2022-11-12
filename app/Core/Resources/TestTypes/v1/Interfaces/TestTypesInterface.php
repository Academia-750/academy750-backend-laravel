<?php
namespace App\Core\Resources\TestTypes\v1\Interfaces;

use App\Models\TestType;

interface TestTypesInterface
{
    public function index();
    public function create( $request );
    public function read( $test_type );
    public function update($request, $test_type );
    public function delete( $test_type );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
