<?php
namespace App\Core\Resources\Tests\v1\Interfaces;

use App\Models\Test;

interface TestsInterface
{
    public function index();
    public function read( $test );

    public function generate( $request );

}
