<?php
namespace App\Core\Resources\Oppositions\v1\Interfaces;

use App\Models\Opposition;

interface OppositionsInterface
{
    public function index();
    public function create( $request );
    public function read( $opposition );
    public function update($request, $opposition );
    public function delete( $opposition );
    public function mass_selection_for_action( $request );
    public function export_records( $request );
    public function import_records( $request );
    public function get_relationship_topics( $opposition );
    public function get_relationship_subtopics( $opposition );
}
