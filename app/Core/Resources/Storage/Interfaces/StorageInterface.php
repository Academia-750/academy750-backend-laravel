<?php
namespace App\Core\Resources\Storage\Interfaces;


interface StorageInterface
{

    public function deleteFolder($workspace);
    public function deleteFile($material);

}