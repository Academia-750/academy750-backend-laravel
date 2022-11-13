<?php
namespace App\Core\Resources\Images\v1\Interfaces;

use App\Models\Image;

interface ImagesInterface
{
    public function index();
    public function create( $request );
    public function read( $image );
    public function update($request, $image );
    public function delete( $image );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
