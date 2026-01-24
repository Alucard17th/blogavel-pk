<?php

declare(strict_types=1);

namespace Blogavel\Blogavel;

use Blogavel\Blogavel\Console\Commands\BlogavelDemoCommand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class BlogavelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blogavel.php', 'blogavel');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BlogavelDemoCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/blogavel.php' => $this->app->configPath('blogavel.php'),
        ], 'blogavel-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blogavel');

        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/blogavel'),
        ], 'blogavel-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/blogavel'),
        ], 'blogavel-lang');

        $this->configureAuthorization();
    }

    private function configureAuthorization(): void
    {
        if (! (bool) config('blogavel.manage_blog_gate', false)) {
            return;
        }

        if (Gate::has('manage-blog')) {
            return;
        }

        Gate::define('manage-blog', function ($user): bool {
            if ((bool) config('blogavel.manage_blog_allow_local', true) && app()->isLocal()) {
                return true;
            }

            $emails = (array) config('blogavel.manage_blog_admin_emails', []);
            if (count($emails) > 0) {
                return in_array((string) $user->email, array_map('strval', $emails), true);
            }

            $ids = (array) config('blogavel.manage_blog_admin_ids', []);
            if (count($ids) > 0) {
                return in_array((int) $user->getAuthIdentifier(), array_map('intval', $ids), true);
            }

            return false;
        });
    }
}
