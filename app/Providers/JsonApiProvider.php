<?php

namespace App\Providers;

use App\Core\JsonApi\JsonApiFilters;
use App\Core\JsonApi\JsonApiIncludes;
use App\Core\JsonApi\JsonApiPagination;
use App\Core\JsonApi\JsonApiSorts;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class JsonApiProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        Builder::mixin(
            new JsonApiSorts
        );
        Builder::mixin(
            new JsonApiFilters
        );
        Builder::mixin(
            new JsonApiIncludes
        );
        Builder::mixin(
            new JsonApiPagination
        );
    }
}
