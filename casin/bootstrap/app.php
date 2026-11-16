<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'auth/session-sync',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($exception->getStatusCode() === 419 && ! $request->expectsJson() && $request->is('logout', 'admin/logout')) {
                if (! Auth::check()) {
                    return redirect()->route('login')->with('status', 'Sesi telah berakhir. Kamu sudah keluar dari akun.');
                }

                return redirect()->route('home')->with('status', 'Sesi halaman telah diperbarui. Silakan klik Keluar kembali.');
            }

            return null;
        });
    })->create();
