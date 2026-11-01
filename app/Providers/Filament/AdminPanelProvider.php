<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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
            ->databaseTransactions()
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('OPES Health Systems')
            ->brandLogo(asset('images/opes-seal.png'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('images/opes-seal.png'))
            ->colors([
                'primary'   => Color::hex('#1664D4'),
                'secondary' => Color::hex('#075985'),
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
                NavigationGroup::make('Confidential')
                    ->collapsible(),
            ])
            ->navigationItems([
                NavigationItem::make('Strategy 2026–2031')
                    ->url(fn () => route('strategy', ['locale' => 'en']))
                    ->icon('heroicon-o-map')
                    ->group('Confidential')
                    ->sort(1)
                    ->openUrlInNewTab(),
                NavigationItem::make('Risk Management')
                    ->url(fn () => route('risk', ['locale' => 'en']))
                    ->icon('heroicon-o-shield-exclamation')
                    ->group('Confidential')
                    ->sort(2)
                    ->openUrlInNewTab(),
                NavigationItem::make('Revenue & Financial Model')
                    ->url(fn () => route('financial-model', ['locale' => 'en']))
                    ->icon('heroicon-o-currency-dollar')
                    ->group('Confidential')
                    ->sort(3)
                    ->openUrlInNewTab(),
                NavigationItem::make('Enterprise Sales Playbook')
                    ->url(fn () => route('sales-playbook', ['locale' => 'en']))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Confidential')
                    ->sort(4)
                    ->openUrlInNewTab(),
                NavigationItem::make('Government Proposal Template')
                    ->url(fn () => route('government-proposal', ['locale' => 'en']))
                    ->icon('heroicon-o-document-text')
                    ->group('Confidential')
                    ->sort(5)
                    ->openUrlInNewTab(),
                NavigationItem::make('Investor Pitch Deck')
                    ->url(fn () => route('investor-pitch', ['locale' => 'en']))
                    ->icon('heroicon-o-presentation-chart-bar')
                    ->group('Confidential')
                    ->sort(6)
                    ->openUrlInNewTab(),
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
/* Seal-based light and dark workspace combinations; no glow/ring shadows. */
:root{--bg:#F3F6FA;--surface:#FFFFFF;--surface-2:#E7EEF5;--text:#102A43;--text-2:#294963;--text-muted:#425A70;--text-faint:#425A70;--brand-action:#1664D4;--brand-accent:#075985;--control-border:#60788F}
.dark{--bg:#081B30;--surface:#102A43;--surface-2:#16354F;--text:#F1F5F9;--text-2:#CBD5E1;--text-muted:#A7B8CC;--text-faint:#9DB1C7;--brand-accent:#69D5F5;--control-border:#7890AA}
*,*::before,*::after{box-shadow:none!important;text-shadow:none!important}
.fi-body{background:var(--bg);color:var(--text)}
.fi-topbar nav,.fi-sidebar,.fi-section{background:var(--surface)}
button,a,svg{filter:none!important}
button:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:2px solid #1664D4;outline-offset:3px}
.dark :focus-visible{outline-color:#A9D8FF}
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
