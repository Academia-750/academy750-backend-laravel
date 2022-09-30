<?php

namespace App\Providers;

use App\Core\Resources\Profile\Authorizer;
use App\Core\Resources\Profile\Interfaces\ProfileInterface;
use Illuminate\Support\ServiceProvider;

class InstanceInterfaceAppProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(ProfileInterface::class, Authorizer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
