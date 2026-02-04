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
   ->withMiddleware(function (Middleware $middleware) {
    // Указываем системе, что после входа нужно идти на /timesheets
    $middleware->redirectTo(
        guests: '/login',
        users: '/timesheets'
    );
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
