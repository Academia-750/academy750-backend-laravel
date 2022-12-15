<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\Storage;

trait ManageImagesStorage
{
    public static function deleteImageStorage ($path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            return true;
        }

        return false;
    }

    public static function getPathForDeleteImageModel ($instanceModel, $separator): string
    {
        $path = $instanceModel?->image?->path;

        $path_array= explode($separator, $path);

        unset($path_array[0], $path_array[1]);

        return implode($separator, $path_array);
    }
}
