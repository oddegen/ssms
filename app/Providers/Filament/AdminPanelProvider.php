<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        /** @var string $admin_url */
        $admin_url = parse_url(config('app.admin_url'), PHP_URL_HOST);

        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->spa()
            ->domain($admin_url)
            ->brandName(fn () => __('SSMS'))
            ->login(Login::class)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label(fn () => __('User Management'))->collapsible(false),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        TextColumn::configureUsing(function (TextColumn $column): void {
            $empty = new HtmlString(sprintf('<small class="opacity-50">&lt;%s&gt;</small>', __('empty')));

            $column->sortable();
            $column->searchable();
            $column->default($empty);
        });

        ImageColumn::configureUsing(function (ImageColumn $column): void {
            $column
                ->disk('local')
                ->visibility('private');
        });

        ImageEntry::configureUsing(function (ImageEntry $entry): void {
            $entry
                ->disk('local')
                ->visibility('private');
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->filtersTriggerAction(
                    fn (Action $action) => $action->slideOver(),
                )
                ->recordClasses(function (Model $model) {
                    if ($model->deleted_at ?? false) {
                        return '
                            bg-red-200/50 dark:bg-red-900/50
                            hover:bg-red-200/100 hover:dark:bg-red-900/100
                        ';
                    }

                    return null;
                });
        });

        FileUpload::configureUsing(function (FileUpload $upload) {
            $upload
                ->disk('local')
                ->visibility('private');
        });

        MarkdownEditor::configureUsing(function (MarkdownEditor $editor) {
            $editor
                ->fileAttachmentsDisk('local')
                ->fileAttachmentsVisibility('private');
        });

        Section::configureUsing(function (Section $section) {
            $section
                ->columnSpan(2);
        });
    }
}
