<?php
namespace App\Core\Resources\Storage\Services;

use App\Core\Resources\Storage\Interfaces\StorageInterface;



/**
 * For test cases or documentation
 */
class DummyStorage implements StorageInterface
{

    public function deleteFolder($workspace)
    {
        return ['status' => 200];
    }
    public function deleteFile($material)
    {
        return ['status' => 200];
    }

}