<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UmkmMiddleware;
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
            'role.admin' => AdminMiddleware::class,
            'role.umkm' => UmkmMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/whatsapp/*',
            'api/whatsapp/webhook',
            'api/webhook',
            'webhook',
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
