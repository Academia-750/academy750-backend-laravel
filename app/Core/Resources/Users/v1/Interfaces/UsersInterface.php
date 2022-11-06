<?php
namespace App\Core\Resources\Users\v1\Interfaces;

interface UsersInterface
{
    public function index();
    public function create( $request );
    public function read( $user );
    public function update($request, $user );
    public function delete( $user );
    public function mass_selection_for_action( $request );
    public function disable_account( $request, $user );
    public function enable_account( $request, $user );
    public function export_records( $request );
    public function import_records( $request );
}
