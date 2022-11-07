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
        app()->bind(\App\Core\Resources\Users\v1\Interfaces\UsersInterface::class, \App\Core\Resources\Users\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface::class, \App\Core\Resources\Oppositions\v1\Authorizer::class);
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
