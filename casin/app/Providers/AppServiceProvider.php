<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, function () {
            return new class implements LogoutResponseContract
            {
                public function toResponse($request): RedirectResponse
                {
                    return redirect()->route('login')->with('status', 'Kamu sudah keluar dari akun.');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
