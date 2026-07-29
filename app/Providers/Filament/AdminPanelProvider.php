<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Analytics;
use App\Filament\Pages\Dashboard;


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
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()


            ->id('admin')
            ->path('admin')
            ->spa()
            ->login()
            ->assets([
                // Sizin əsas Tailwind CSS buildinizi Filament daxilinə yükləyir
                \Filament\Support\Assets\Css::make('custom-styles', \Illuminate\Support\Facades\Vite::asset('resources/css/app.css')),
                // Və ya Vite birbaşa istifadə olunursa:
                // \Filament\Support\Assets\Css::make('custom-styles', \Illuminate\Support\Facades\Vite::asset('resources/css/app.css')),
            ])
            ->navigationGroups([
                'SATIŞ',
                'KASSA',
                'MAĞAZA',

                'TƏCHİZAT',
                'MÜŞTƏRİLƏR',
                'MÜŞTƏRİ BORCLARI',

                'SİSTEM',
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
          //  ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
               Dashboard::class,
               Analytics::class,
            ])
           // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([

            ])
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
