<?php
namespace App\Core\Resources\Tests\v1\Interfaces;

use App\Models\Test;

interface TestsInterface
{
    public function get_tests_unresolved();
    public function get_cards_memory();
    public function fetch_unresolved_test( $test );
    public function fetch_card_memory( $test );

    public function create_a_quiz( $request );

}
