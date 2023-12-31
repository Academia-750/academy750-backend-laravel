<?php

namespace App\Core\Services;

use App\Models\User;

trait UUIDTrait
{
    public static function bootUuidTrait(): void
    {
        static::creating(static function ($model) {
            /*$model->keyType = 'string';
            $model->incrementing = false;*/

            $model->{$model->getRouteKeyName()} = UuidGeneratorService::getUUIDUnique($model, $model->getRouteKeyName());
        });
    }

}
