<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Analytics;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\POS;
use App\Filament\Pages\Reports\InventoryReport;
use App\Filament\Pages\Reports\ProfitReport;
use App\Filament\Pages\Reports\SalesReport;
use App\Filament\Pages\Settings;
use App\Models\Settings as ModelsSettings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->databaseTransactions()
            ->brandName(fn() => Schema::hasTable('settings')
                ? (ModelsSettings::current()->app_name ?: config('app.name'))
                : config('app.name'))
            ->brandLogo(function () {
                if (! Schema::hasTable('settings')) {
                    return null;
                }
                $logo = ModelsSettings::current()->logo;

                return filled($logo) ? Storage::disk('public')->url($logo) : null;
            })
            ->brandLogoHeight('2rem')

            ->id('admin')
            ->path('admin')
            ->spa()
            ->login()
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup(__('resource.navigationGroup.system'))
                    ->navigationSort(3),
            ])
            ->assets([
                // Sizin əsas Tailwind CSS buildinizi Filament daxilinə yükləyir
                \Filament\Support\Assets\Css::make('custom-styles', \Illuminate\Support\Facades\Vite::asset('resources/css/app.css')),
                // Və ya Vite birbaşa istifadə olunursa:
                // \Filament\Support\Assets\Css::make('custom-styles', \Illuminate\Support\Facades\Vite::asset('resources/css/app.css')),
            ])
            ->navigationGroups([

                __('resource.navigationGroup.analyse'),
                __('resource.navigationGroup.inventory'),
                __('resource.navigationGroup.sale'),
                __('resource.navigationGroup.shop'),
                __('resource.navigationGroup.supplier'),
                __('resource.navigationGroup.customer'),
                __('resource.navigationGroup.system'),



            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            //  ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                Analytics::class,
                POS::class,
                InventoryReport::class,
                Settings::class,
                //   SalesReport::class,
                // ProfitReport::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
