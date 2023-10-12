<?php
namespace App\Core\Resources\Oppositions\v1\Interfaces;

interface OppositionsInterface
{
    public function index($request);
    public function create($request);
    public function read($opposition);
    public function update($request, $opposition);
    public function delete($opposition);
    public function mass_selection_for_action($request);
    public function get_relationship_syllabus($opposition);
}