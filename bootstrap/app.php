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
        $middleware->alias([
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
            'check.status'       => \App\Http\Middleware\CheckUserStatus::class,
            'log.activity'       => \App\Http\Middleware\LogUserActivity::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role.or.permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'session.timeout'    => \App\Http\Middleware\SessionTimeout::class,
        ]);

        $middleware->append(\App\Http\Middleware\CheckUserStatus::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\SessionTimeout::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
