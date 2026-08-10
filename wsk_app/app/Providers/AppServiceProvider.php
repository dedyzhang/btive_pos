<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        view()->composer(['layout.index'], function ($view) {
            if (Auth::check()) {
                $account = Auth::user();
                $view->with('account', $account);
            }
        });

        Gate::define('admin', function ($user) {
            return $user->role === "admin";
        });
        Gate::define('cashier', function ($user) {
            return $user->role === "cashier";
        });

        $permissions = [
            'access_admin_dashboard',
            'access_cashier',
            'manage_products',
            'manage_categories',
            'view_reports',
            'manage_settings',
            'manage_attendance',
            'manage_users',
            'manage_cashflow',
            'view_kitchen_queue'
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
