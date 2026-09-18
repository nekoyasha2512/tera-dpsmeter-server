<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

public function boot()
{
    // 確保應用程式平時執行 SQL 時，不會被 Aiven 的 Primary Key 與 SSL 限制擋下
    \DB::statement('SET SESSION sql_require_primary_key = 0;');
    \Schema::defaultStringLength(191);
}
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
