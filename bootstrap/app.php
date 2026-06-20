<?php

use App\Console\Commands\SendDailyAgenda;
use App\Console\Commands\SendScheduleReminders;
use App\Console\Commands\SetTelegramWebhook;
use App\Console\Commands\SyncGoogleIntegrations;
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
    ->withCommands([
        SendScheduleReminders::class,
        SetTelegramWebhook::class,
        SendDailyAgenda::class,
        SyncGoogleIntegrations::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
