<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Core\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Pluralizer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::guessPolicyNamesUsing(function ($model) {
            return 'App\\Policies\\Api\\v1\\'. Pluralizer::singular(class_basename($model)).'Policy';
        });

        AuthService::RemoveExpiredTokensAction();
    }
}
