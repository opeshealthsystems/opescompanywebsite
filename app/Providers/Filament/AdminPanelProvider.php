<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('OPES Health Systems')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary'   => Color::hex('#00C896'),
                'secondary' => Color::hex('#1A6FE8'),
                'gray'      => Color::Slate,
            ])
            ->navigationGroups([
                NavigationGroup::make('People'),
                NavigationGroup::make('Practitioners'),
                NavigationGroup::make('Learning'),
                NavigationGroup::make('Platform'),
                NavigationGroup::make('CRM'),
                NavigationGroup::make('Licenses'),
                NavigationGroup::make('Support'),
                NavigationGroup::make('Operations'),
                NavigationGroup::make('Accounting'),
                NavigationGroup::make('Documents'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Communications'),
                NavigationGroup::make('Reporting'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
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
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => <<<'HTML'
<style>
/* ── Grid column utilities missing from Filament's bundled CSS ── */
.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
.grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
.grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
.grid-cols-5{grid-template-columns:repeat(5,minmax(0,1fr))}
.grid-cols-6{grid-template-columns:repeat(6,minmax(0,1fr))}
@media(min-width:640px){
  .sm\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
  .sm\:grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
}
@media(min-width:768px){
  .md\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
  .md\:grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
}
@media(min-width:1024px){
  .lg\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
  .lg\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
  .lg\:grid-cols-6{grid-template-columns:repeat(6,minmax(0,1fr))}
}
/* ── Other missing utilities ── */
.min-h-20{min-height:5rem}
.tracking-widest{letter-spacing:.1em}
</style>
HTML
            );
    }
}
