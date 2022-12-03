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
        app()->bind(\App\Core\Resources\Topics\v1\Interfaces\TopicsInterface::class, \App\Core\Resources\Topics\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface::class, \App\Core\Resources\Subtopics\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface::class, \App\Core\Resources\TopicGroups\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface::class, \App\Core\Resources\Questions\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Tests\v1\Interfaces\TestsInterface::class, \App\Core\Resources\Tests\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Answers\v1\Interfaces\AnswersInterface::class, \App\Core\Resources\Answers\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface::class, \App\Core\Resources\TestTypes\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\Images\v1\Interfaces\ImagesInterface::class, \App\Core\Resources\Images\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface::class, \App\Core\Resources\ImportProcesses\v1\Authorizer::class);
        app()->bind(\App\Core\Resources\ImportRecords\v1\Interfaces\ImportRecordsInterface::class, \App\Core\Resources\ImportRecords\v1\Authorizer::class);
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
