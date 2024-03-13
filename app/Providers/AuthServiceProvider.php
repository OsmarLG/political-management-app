<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('MASTER') ? true : null;
        });
    }
}
