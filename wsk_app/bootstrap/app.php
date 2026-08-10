<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->redirectGuestsTo('/');
         $middleware->redirectUsersTo(function() {
             if (auth()->check()) {
                 $user = auth()->user();
                 if ($user->role === 'admin' || $user->hasPermission('access_admin_dashboard')) {
                     return '/admin/dashboard';
                 }
                 if ($user->hasPermission('view_kitchen_queue') && !$user->hasPermission('access_cashier')) {
                     return '/kitchen/queue';
                 }
                 return '/dashboard';
             }
             return '/';
         });
         $middleware->alias([
             'permission' => \App\Http\Middleware\CheckPermission::class,
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
