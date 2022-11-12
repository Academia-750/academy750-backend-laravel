<?php
namespace App\Core\Resources\Tests\v1\Interfaces;

use App\Models\Test;

interface TestsInterface
{
    public function index();
    public function create( $request );
    public function read( $test );
    public function update($request, $test );
    public function delete( $test );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
