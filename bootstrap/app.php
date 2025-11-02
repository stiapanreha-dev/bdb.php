<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/payment/webhook',
            'payment/webhook',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Send newsletters hourly (actual interval controlled by settings)
        $schedule->command('newsletters:send')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Renew expired newsletter subscriptions hourly (actual time controlled by settings)
        $schedule->command('newsletters:renew')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Обработка CSRF ошибок (истекшая сессия)
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->back()->with('error',
                'Ваша сессия истекла. Пожалуйста, повторите действие.'
            );
        });
    })->create();
