<?php

namespace App\Providers;

use App\Enums\UserRoleEnums;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::define('isAdmin', function (User $user) {
            return $user->role->name === 'ADMIN';
            //when we called with (name) it changes to string datatype and capitalize
        });
    }
}