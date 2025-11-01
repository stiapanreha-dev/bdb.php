<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Send newsletters every 3 hours
        $schedule->command('newsletters:send')
            ->everyThreeHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Renew expired newsletter subscriptions daily at 00:00
        $schedule->command('newsletters:renew')
            ->daily()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
