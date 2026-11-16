<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Middleware\AuthenticateAdmin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('CasIn.id Admin')
            ->brandLogo(fn () => view('partials.brand-logo'))
            ->homeUrl('/')
            ->font('Poppins')
            ->colors([
                'primary' => Color::hex('#00b3c7'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('Data User')
                    ->icon(Heroicon::OutlinedUsers)
                    ->url('/admin#users')
                    ->sort(2),
                NavigationItem::make('Data Stasiun')
                    ->icon(Heroicon::OutlinedBolt)
                    ->url('/admin#stations')
                    ->sort(3),
                NavigationItem::make('Data Kendaraan')
                    ->icon(Heroicon::OutlinedTruck)
                    ->url('/admin#vehicles')
                    ->sort(4),
                NavigationItem::make('Data Konektor')
                    ->icon(Heroicon::OutlinedCpuChip)
                    ->url('/admin#connectors')
                    ->sort(5),
                NavigationItem::make('Data Laporan')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->url('/admin#reports')
                    ->sort(6),
                NavigationItem::make('Data Ulasan')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->url('/admin#reviews')
                    ->sort(7),
                NavigationItem::make('Profil Admin')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->url('/admin#profile')
                    ->sort(8),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                AuthenticateAdmin::class,
            ])
            ->authenticatedRoutes(function () {
                Route::prefix('api')->group(function () {
                    Route::get('/stations', [AdminDataController::class, 'listStations'])->name('api.stations.list');
                    Route::get('/station-options', [AdminDataController::class, 'listStationOptions'])->name('api.stations.options');
                    Route::post('/stations', [AdminDataController::class, 'storeStation'])->name('api.stations.store');
                    Route::put('/stations/{id}', [AdminDataController::class, 'updateStation'])->name('api.stations.update');
                    Route::delete('/stations/{id}', [AdminDataController::class, 'destroyStation'])->name('api.stations.destroy');

                    Route::post('/vehicles', [AdminDataController::class, 'storeVehicle'])->name('api.vehicles.store');
                    Route::put('/vehicles/{id}', [AdminDataController::class, 'updateVehicle'])->name('api.vehicles.update');
                    Route::delete('/vehicles/{id}', [AdminDataController::class, 'destroyVehicle'])->name('api.vehicles.destroy');

                    Route::get('/connectors', [AdminDataController::class, 'listConnectors'])->name('api.connectors.list');
                    Route::post('/connectors', [AdminDataController::class, 'storeConnector'])->name('api.connectors.store');
                    Route::put('/connectors/{id}', [AdminDataController::class, 'updateConnector'])->name('api.connectors.update');
                    Route::delete('/connectors/{id}', [AdminDataController::class, 'destroyConnector'])->name('api.connectors.destroy');

                    Route::patch('/users/{id}/role', [AdminDataController::class, 'updateUserRole'])->name('api.users.role');
                    Route::delete('/users/{id}', [AdminDataController::class, 'destroyUser'])->name('api.users.destroy');

                    Route::patch('/reports/{id}/status', [AdminDataController::class, 'updateReportStatus'])->name('api.reports.status');
                    Route::delete('/reports/{id}', [AdminDataController::class, 'destroyReport'])->name('api.reports.destroy');

                    Route::delete('/reviews/{id}', [AdminDataController::class, 'destroyReview'])->name('api.reviews.destroy');

                    Route::patch('/profile', [AdminDataController::class, 'updateProfile'])->name('api.profile.update');
                });
            });
    }
}
