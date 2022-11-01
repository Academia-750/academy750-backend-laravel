<?php

namespace App\Providers;

use App\Core\Resources\Profile\v1\Authorizer;
use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
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
        app()->bind(\App\Core\Resources\Students\v1\Interfaces\StudentsInterface::class, \App\Core\Resources\Students\v1\Authorizer::class);
        // [EndOfLineMethodRegister]
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
