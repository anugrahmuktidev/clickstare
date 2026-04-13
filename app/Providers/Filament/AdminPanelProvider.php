<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use App\Filament\Pages\Auth\ChangePassword;
use Filament\Navigation\MenuItem;
use Filament\View\PanelsRenderHook;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentTimezone::set('Asia/Jakarta'); // v4
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('SAARe')
            ->profile(ChangePassword::class, isSimple: false)
            ->userMenuItems([
                'profile' => MenuItem::make()->visible(false),
                'change-password' => MenuItem::make()
                    ->label('Ubah Password')
                    ->icon(Heroicon::OutlinedKey)
                    ->url(fn (): string => filament()->getProfileUrl() ?? filament()->getUrl())
                    ->sort(PHP_INT_MAX - 1),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => <<<'HTML'
                    <style>
                        .fi-no {
                            z-index: 70 !important;
                        }

                        .fi-no-notification,
                        .fi-no-notification .fi-no-notification-close-btn {
                            pointer-events: auto !important;
                        }
                    </style>
                HTML,
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn (): string => <<<'HTML'
                    <script data-navigate-once>
                        if (! window.__filamentNotificationCloseFixInstalled) {
                            window.__filamentNotificationCloseFixInstalled = true;

                            document.addEventListener('click', (event) => {
                                const closeButton = event.target.closest('.fi-no-notification-close-btn');

                                if (! closeButton) {
                                    return;
                                }

                                const notificationElement = closeButton.closest('.fi-no-notification');

                                if (! notificationElement) {
                                    return;
                                }

                                const alpineData = notificationElement._x_dataStack?.[0];

                                if (typeof alpineData?.close === 'function') {
                                    alpineData.close();

                                    return;
                                }

                                const wireKey = notificationElement.getAttribute('wire:key') ?? '';
                                const notificationId = wireKey.split('.notifications.').pop();

                                if (notificationId) {
                                    window.dispatchEvent(
                                        new CustomEvent('close-notification', {
                                            detail: { id: notificationId },
                                        }),
                                    );
                                }
                            });
                        }
                    </script>
                HTML,
            )
            // ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                // FilamentInfoWidget::class,
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
                Authenticate::class,
                \App\Http\Middleware\EnsureRole::class . ':admin',

            ])->authGuard('web');
    }
}
