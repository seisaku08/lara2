<?php

namespace App\Providers;

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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

    // 管理者以上に許可
    Gate::define('sys-ad', function ($user) {
    return ($user->role == 1);
    });

    // 大應に許可
    Gate::define('daioh', function ($user) {
    return ($user->role >= 2 && $user->role <= 10);
    });

    // Reservation operations are available to both system administrators and Daioh users.
    Gate::define('reservation-operator', function ($user) {
        return $user->role == 1 || ($user->role >= 2 && $user->role <= 10);
    });
        
        //
    }
}
