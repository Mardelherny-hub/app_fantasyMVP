<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // (Opcional) Alias si querés usarlo por ruta: ->middleware('setlocale')
        // $middleware->alias([
        //     'setlocale' => \App\Http\Middleware\SetLocale::class,
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
