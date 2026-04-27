<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Verifica e encerra automaticamente ouvidorias inativas a cada hora.
        // Em produção, configure o cron: * * * * * php /path/to/artisan schedule:run
        $schedule->command('ouvidoria:auto-encerrar')->hourly();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Alias para uso simplificado nas rotas
        $middleware->alias([
            'rh_or_admin'  => \App\Http\Middleware\EnsureRhOrAdmin::class,
            'can_evaluate' => \App\Http\Middleware\EnsureCanEvaluate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
