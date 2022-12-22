<?php
namespace App\Core\Resources\Tests\v1\Interfaces;

use App\Models\Test;

interface TestsInterface
{
    public function get_tests_unresolved();
    public function fetch_unresolved_test( $test );

    public function create_a_quiz( $request );

}
