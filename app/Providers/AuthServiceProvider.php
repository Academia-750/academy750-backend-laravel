<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
            return 'App\\Policies\\Api\\'. Pluralizer::plural(class_basename($model)).'Policy';
        });

        /* Por cada petición, antes de procesar la operación, eliminaremos los tokens que ya están expirados */
        $recordsTokenExpired = DB::table('personal_access_tokens')->where('expires_at', '<', Carbon::now())->pluck('id');

        if ($recordsTokenExpired->count() > 0 ) {
            foreach ($recordsTokenExpired as $token_id) {
                DB::table('personal_access_tokens')->where('id', '=', $token_id)->delete();
            }
        }
    }
}
