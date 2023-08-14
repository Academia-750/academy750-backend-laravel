<?php
namespace App\Core\Resources\Storage;

use App\Core\Resources\Storage\Services\CloudinaryStorage;
use App\Core\Resources\Storage\Services\DummyStorage;
use App\Core\Resources\Storage\Interfaces\StorageInterface;



/**
 * We may use different providers, this class is the abstraction that chose the correct provider
 * for each url
 */
class Storage
{
    public static function for ($material): StorageInterface
    {

        if (in_array(config('app.env'), ['testing', 'documentation'])) {
            return app()->make(DummyStorage::class);
        }

        return app()->make(CloudinaryStorage::class);
    }

    public static function all(): array
    {

        if (in_array(config('app.env'), ['testing', 'documentation'])) {
            return [app()->make(DummyStorage::class)];
        }

        return [app()->make(CloudinaryStorage::class)];
    }

}